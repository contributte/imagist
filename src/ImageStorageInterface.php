<?php declare(strict_types = 1);

namespace Contributte\Imagist;

use Contributte\Imagist\Context\Context;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;

interface ImageStorageInterface
{

	public function persist(ImageInterface $image, ?Context $context = null): PersistentImageInterface;

	public function remove(PersistentImageInterface $image, ?Context $context = null): PersistentImageInterface;

}
