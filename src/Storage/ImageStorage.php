<?php declare(strict_types = 1);

namespace Contributte\Imagist\Storage;

use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Event\PersistedImageEvent;
use Contributte\Imagist\Event\RemovedImageEvent;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\Persister\PersisterRegistryInterface;
use Contributte\Imagist\Remover\RemoverRegistryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ImageStorage implements ImageStorageInterface
{

	private PersisterRegistryInterface $persisterRegistry;

	private RemoverRegistryInterface $removerRegistry;

	private ?EventDispatcherInterface $dispatcher;

	public function __construct(
		PersisterRegistryInterface $persisterRegistry,
		RemoverRegistryInterface $removerRegistry,
		?EventDispatcherInterface $dispatcher = null
	)
	{
		$this->persisterRegistry = $persisterRegistry;
		$this->removerRegistry = $removerRegistry;
		$this->dispatcher = $dispatcher;
	}

	public function persist(ImageInterface $image): PersistentImageInterface
	{
		$clone = clone $image;
		$result = $this->persisterRegistry->persist($image);
		$persistent = new PersistentImage($result->getId());

		if ($clone->getFilter()) {
			$persistent = $persistent->withFilterObject($clone->getFilter());
		}

		if ($this->dispatcher) {
			$this->dispatcher->dispatch(new PersistedImageEvent($this, $clone, $persistent));
		}

		return $persistent;
	}

	public function remove(PersistentImageInterface $image): PersistentImageInterface
	{
		if ($image->isClosed()) {
			return new EmptyImage();
		}

		$clone = clone $image;
		$this->removerRegistry->remove($image);

		if ($this->dispatcher) {
			$this->dispatcher->dispatch(new RemovedImageEvent($this, $clone));
		}

		return new EmptyImage();
	}

}
