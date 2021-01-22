<?php declare(strict_types = 1);

namespace Contributte\Imagist;

use Contributte\Imagist\Entity\PersistentImageInterface;

interface LinkGeneratorInterface
{

	/**
	 * @param mixed[] $options
	 */
	public function link(?PersistentImageInterface $image, array $options = []): ?string;

}
