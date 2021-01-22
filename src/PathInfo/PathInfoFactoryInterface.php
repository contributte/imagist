<?php declare(strict_types = 1);

namespace Contributte\Imagist\PathInfo;

use Contributte\Imagist\Entity\ImageInterface;

interface PathInfoFactoryInterface
{

	public function create(ImageInterface $image): PathInfoInterface;

}
