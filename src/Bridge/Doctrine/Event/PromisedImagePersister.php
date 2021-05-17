<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Doctrine\Event;

use Contributte\Imagist\Entity\PersistentImageInterface;

interface PromisedImagePersister
{

	/**
	 * @return (PersistentImageInterface|null)[]
	 */
	public function _promisedImagesToPersist(): array;

}
