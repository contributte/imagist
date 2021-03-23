<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Doctrine\Event;

use Contributte\Imagist\Entity\PersistentImageInterface;

interface ImageCleaner
{

	/**
	 * @return PersistentImageInterface[]
	 */
	public function _imagesToClean(): array;

}
