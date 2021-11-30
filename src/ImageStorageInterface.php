<?php declare(strict_types = 1);

namespace Contributte\Imagist;

use Contributte\Imagist\Filter\Context\Context;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;

interface ImageStorageInterface
{

	/**
	 * @param mixed[] $context
	 */
	public function persist(ImageInterface $image, array $context = []): PersistentImageInterface;

	/**
	 * @param mixed[] $context
	 */
	public function remove(PersistentImageInterface $image, array $context = []): PersistentImageInterface;

}
