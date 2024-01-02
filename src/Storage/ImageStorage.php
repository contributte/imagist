<?php declare(strict_types = 1);

namespace Contributte\Imagist\Storage;

use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Event\PersistedImageEvent;
use Contributte\Imagist\Event\RemovedImageEvent;
use Contributte\Imagist\Filter\Context\ContextFactory;
use Contributte\Imagist\Filter\Context\ContextFactoryInterface;
use Contributte\Imagist\Filter\StringFilter\StringFilterCollectionInterface;
use Contributte\Imagist\Filter\StringFilter\StringFilterFacade;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\Persister\PersisterInterface;
use Contributte\Imagist\Remover\RemoverInterface;
use LogicException;
use Nette\Utils\Arrays;
use Psr\EventDispatcher\EventDispatcherInterface;

class ImageStorage implements ImageStorageInterface
{

	/** @var array<callable(PersistedImageEvent): void> */
	public array $onPersist = [];

	/** @var array<callable(RemovedImageEvent): void> */
	public array $onRemove = [];

	private PersisterInterface $persister;

	private RemoverInterface $remover;

	private ?EventDispatcherInterface $dispatcher;

	private ?StringFilterCollectionInterface $stringFilterCollection;

	private ContextFactoryInterface $contextFactory;

	public function __construct(
		PersisterInterface $persister,
		RemoverInterface $remover,
		?ContextFactoryInterface $contextFactory = null,
		?StringFilterCollectionInterface $stringFilterCollection = null,
		?EventDispatcherInterface $dispatcher = null
	)
	{
		$this->persister = $persister;
		$this->remover = $remover;
		$this->contextFactory = $contextFactory ?? new ContextFactory();
		$this->stringFilterCollection = $stringFilterCollection;
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @param mixed[] $context
	 */
	public function persist(ImageInterface $image, array $context = []): PersistentImageInterface
	{
		$image = StringFilterFacade::resolveByImage($this->stringFilterCollection, $image);

		$context = $this->contextFactory->create($context);
		$clone = clone $image;

		if (!$this->persister->supports($image, $context)) {
			throw new LogicException('Persister not found.');
		}

		$result = $this->persister->persist($image, $context);
		$persistent = new PersistentImage($result->getId());

		if ($clone->getFilter() !== null) {
			$persistent = $persistent->withFilter($clone->getFilter());
		}

		$event = new PersistedImageEvent($this, $clone, $persistent);

		if ($this->dispatcher !== null) {
			$this->dispatcher->dispatch($event);
		}

		Arrays::invoke($this->onPersist, $event);

		return $persistent;
	}

	/**
	 * @param mixed[] $context
	 */
	public function remove(PersistentImageInterface $image, array $context = []): PersistentImageInterface
	{
		if ($image->isClosed()) {
			return new EmptyImage();
		}

		$context = $this->contextFactory->create($context);
		$clone = clone $image;

		if (!$this->remover->supports($image, $context)) {
			throw new LogicException('Remover not found.');
		}

		$this->remover->remove($image, $context);

		$event = new RemovedImageEvent($this, $clone);

		if ($this->dispatcher) {
			$this->dispatcher->dispatch(new RemovedImageEvent($this, $clone));
		}

		Arrays::invoke($this->onRemove, $event);

		return new EmptyImage();
	}

}
