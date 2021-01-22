<?php declare(strict_types = 1);

namespace Contributte\Imagist;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;

interface ImageStorageInterface
{

	public function persist(ImageInterface $image): PersistentImageInterface;

	public function remove(PersistentImageInterface $image): PersistentImageInterface;

}
