<?php declare(strict_types = 1);

namespace Contributte\Imagist\Transaction;

use Contributte\Imagist\Filter\Context\Context;
use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\PromisedImage;
use Contributte\Imagist\Entity\PromisedImageInterface;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\Exceptions\FileNotFoundException;
use Contributte\Imagist\Exceptions\RollbackFailedException;
use Contributte\Imagist\Exceptions\TransactionException;
use Contributte\Imagist\File\FileFactoryInterface;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\Transaction\Entity\RemovedImage;
use Contributte\Imagist\Transaction\Entity\RemoveImage;
use Contributte\Imagist\Uploader\StringUploader;
use Google\Cloud\Core\Exception\NotFoundException;
use InvalidArgumentException;
use Throwable;

final class Transaction implements TransactionInterface
{

	private ImageStorageInterface $imageStorage;

	private bool $committed = false;

	private FileFactoryInterface $fileFactory;

	/** @var PromisedImageInterface[] */
	private array $persist = [];

	/** @var PersistentImageInterface[] */
	private array $persisted = [];

	/** @var RemoveImage[] */
	private array $remove = [];

	/** @var RemovedImage[] */
	private array $removed = [];

	public function __construct(ImageStorageInterface $imageStorage, FileFactoryInterface $fileFactory)
	{
		$this->imageStorage = $imageStorage;
		$this->fileFactory = $fileFactory;
	}

	public function isCommitted(): bool
	{
		return $this->committed;
	}

	public function commit(): void
	{
		if ($this->committed) {
			throw new TransactionException('Transaction is already commited');
		}

		$this->committed = true;

		$this->commitRemove();
		$this->commitPersist();
	}

	/**
	 * @inheritDoc
	 */
	public function rollback(): void
	{
		if (!$this->committed) {
			throw new TransactionException('Transaction is not commited');
		}

		$exception = null;
		foreach ($this->removed as $image) {
			if (!$image->isRemoved()) {
				continue;
			}

			try {
				$store = new StorableImage(
					new StringUploader($image->getContent()),
					$image->getSource()->getName()
				);
				$store = $store->withScope($image->getSource()->getScope());

				$this->imageStorage->persist($store);
			} catch (Throwable $exception) {
				// no need
			}
		}

		foreach ($this->persisted as $image) {
			try {
				$this->imageStorage->remove($image);
			} catch (Throwable $exception) {
				// no need
			}
		}

		$this->persisted = [];
		$this->removed = [];

		if ($exception) {
			throw new RollbackFailedException(
				sprintf('Rollback failed because of: %s', $exception->getMessage()),
				0,
				$exception
			);
		}
	}

	/**
	 * @param mixed[] $context
	 */
	public function persist(ImageInterface $image, array $context = []): PromisedImageInterface
	{
		if ($image instanceof PromisedImageInterface) {
			foreach ($this->remove as $key => $removed) {
				if ($removed->getPromisedImage() === $image) {
					unset($this->remove[$key]);

					$image->process(fn (ImageInterface $img) => $img);

					return $image;
				}
			}

			throw new InvalidArgumentException(sprintf('Cannot persist promised image twice'));
		}

		return $this->persist[] = new PromisedImage($this, $image, false);
	}

	/**
	 * @param mixed[] $context
	 */
	public function remove(PersistentImageInterface $image, array $context = []): PromisedImageInterface
	{
		if ($image instanceof PromisedImageInterface) {
			if ($image->isPending()) {
				foreach ($this->persist as $key => $persisted) {
					if ($image === $persisted) {
						unset($this->persist[$key]);

						$image->process(fn (ImageInterface $image) => new EmptyImage(clone $image->getScope()));

						return $image;
					}
				}

				throw new InvalidArgumentException(sprintf('Cannot remove promised image twice'));
			} else {
				$image = $image->getResult();
			}
		}

		$promised = new PromisedImage($this, $image, true);
		$this->remove[] = new RemoveImage($image, $promised);

		return $promised;
	}

	private function commitRemove(): void
	{
		foreach ($this->remove as $key => $image) {
			try {
				$this->removed[] = new RemovedImage(
					clone $image->getSource(),
					$image->getPromisedImage(),
					$this->fileFactory->create($image->getSource())->getContent()
				);
			} catch (NotFoundException | FileNotFoundException $e) {
				// object not exists continue
			}
		}

		foreach ($this->removed as $image) {
			$image->getPromisedImage()->process([$this->imageStorage, 'remove']);

			$image->setRemoved();
		}
	}

	private function commitPersist(): void
	{
		foreach ($this->persist as $image) {
			try {
				$image->process([$this->imageStorage, 'persist']);

				$this->persisted[] = $image->getResult();
			} catch (Throwable $e) {
				$this->rollback();

				throw new TransactionException('Transaction failed', 0, $e);
			}
		}
	}

}
