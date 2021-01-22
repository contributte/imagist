<?php declare(strict_types = 1);

namespace Contributte\Imagist\Persister;

use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Exceptions\InvalidArgumentException;

final class EmptyImagePersister implements PersisterInterface
{

	private bool $strict = true;

	public function setStrict(bool $strict): void
	{
		$this->strict = $strict;
	}

	public function supports(ImageInterface $image): bool
	{
		return $image instanceof EmptyImageInterface;
	}

	public function persist(ImageInterface $image): PersistentImageInterface
	{
		if ($this->strict) {
			throw new InvalidArgumentException('Cannot persist empty image');
		}

		return new EmptyImage();
	}

}
