<?php declare(strict_types = 1);

namespace Contributte\Imagist\PathInfo;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\StorableImageInterface;
use Contributte\Imagist\Resolver\BucketResolverInterface;
use Contributte\Imagist\Resolver\BucketResolvers\BucketResolver;
use Contributte\Imagist\Resolver\FilterResolverInterface;
use Contributte\Imagist\Resolver\FilterResolvers\OriginalFilterResolver;

final class PathInfoFactory implements PathInfoFactoryInterface
{

	private FilterResolverInterface $filterResolver;

	private BucketResolverInterface $bucketResolver;

	public function __construct(?FilterResolverInterface $filterResolver = null, ?BucketResolverInterface $bucketResolver = null)
	{
		$this->filterResolver = $filterResolver ?? new OriginalFilterResolver();
		$this->bucketResolver = $bucketResolver ?? new BucketResolver();
	}

	public function create(ImageInterface $image): PathInfoInterface
	{
		$filter = null;
		if (!$image instanceof StorableImageInterface && ($imageFilter = $image->getFilter())) {
			$filter = $this->filterResolver->resolve($imageFilter);
		}

		return new PathInfo($this->bucketResolver->resolve($image), $image->getScope()->toString(), $filter, $image->getName());
	}

}
