<?php declare(strict_types = 1);

namespace Contributte\Imagist\File;

use Contributte\Imagist\Entity\ImageInterface;

interface FileFactoryInterface
{

	public function create(ImageInterface $image): FileInterface;

}
