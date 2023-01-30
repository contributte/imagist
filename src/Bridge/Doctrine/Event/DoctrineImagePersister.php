<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Doctrine\Event;

use Contributte\Imagist\Entity\PersistentImageInterface;

interface DoctrineImagePersister
{

	/**
	 * @return array<PersistentImageInterface|null>
	 */
	public function _imagesToPersist(): array;

}
