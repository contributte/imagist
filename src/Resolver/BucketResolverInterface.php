<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver;

use Contributte\Imagist\Entity\ImageInterface;

interface BucketResolverInterface
{

	public function resolve(ImageInterface $image): string;

}
