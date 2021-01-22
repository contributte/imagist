<?php declare(strict_types = 1);

namespace Contributte\Imagist\Event;

use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\ImageStorageInterface;
use Psr\EventDispatcher\StoppableEventInterface;

final class RemovedImageEvent implements StoppableEventInterface
{

	use StoppableEvent;

	private ImageStorageInterface $context;

	private PersistentImageInterface $image;

	public function __construct(ImageStorageInterface $context, PersistentImageInterface $image)
	{
		$this->context = $context;
		$this->image = $image;
	}

	public function getSource(): PersistentImageInterface
	{
		return $this->image;
	}

	public function getContext(): ImageStorageInterface
	{
		return $this->context;
	}

}
