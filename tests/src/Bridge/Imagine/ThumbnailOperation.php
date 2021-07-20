<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Bridge\Imagine;

use Contributte\Imagist\Bridge\Imagine\OperationInterface;
use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Filter\FilterInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class ThumbnailOperation implements OperationInterface
{

	public function supports(FilterInterface $filter, ContextImageAware $context): bool
	{
		return $filter->getName() === 'thumbnail';
	}

	public function operate(ImageInterface $image, FilterInterface $filter, ContextImageAware $context): void
	{
		$image->resize(new Box(15, 15));
	}

}
