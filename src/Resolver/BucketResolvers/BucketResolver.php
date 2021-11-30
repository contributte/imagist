<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\BucketResolvers;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\StorableImageInterface;
use Contributte\Imagist\Resolver\BucketResolverInterface;

final class BucketResolver implements BucketResolverInterface
{

	public function resolve(ImageInterface $image): string
	{
		if ($image instanceof StorableImageInterface || !$image->hasFilter()) {
			return 'media';
		}

		return 'cache';
	}

}
