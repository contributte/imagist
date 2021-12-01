<?php declare(strict_types = 1);

namespace Contributte\Imagist\Storage;

use Contributte\Imagist\Filter\Context\Context;
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
use Contributte\Imagist\Persister\PersisterRegistryInterface;
use Contributte\Imagist\Remover\RemoverRegistryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ImageStorage implements ImageStorageInterface
{

	private PersisterRegistryInterface $persisterRegistry;

	private RemoverRegistryInterface $removerRegistry;

	private ?EventDispatcherInterface $dispatcher;

	private ?StringFilterCollectionInterface $stringFilterCollection;

	private ContextFactoryInterface $contextFactory;

	public function __construct(
		PersisterRegistryInterface $persisterRegistry,
		RemoverRegistryInterface $removerRegistry,
		?ContextFactoryInterface $contextFactory = null,
		?StringFilterCollectionInterface $stringFilterCollection = null,
		?EventDispatcherInterface $dispatcher = null
	)
	{
		$this->persisterRegistry = $persisterRegistry;
		$this->removerRegistry = $removerRegistry;
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
		$result = $this->persisterRegistry->persist($image, $context);
		$persistent = new PersistentImage($result->getId());

		if ($clone->getFilter()) {
			$persistent = $persistent->withFilter($clone->getFilter());
		}

		if ($this->dispatcher) {
			$this->dispatcher->dispatch(new PersistedImageEvent($this, $clone, $persistent));
		}

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
		$this->removerRegistry->remove($image, $context);

		if ($this->dispatcher) {
			$this->dispatcher->dispatch(new RemovedImageEvent($this, $clone));
		}

		return new EmptyImage();
	}

}
