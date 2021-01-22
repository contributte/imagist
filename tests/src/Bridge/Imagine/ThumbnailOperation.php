<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Bridge\Imagine;

use Contributte\Imagist\Bridge\Imagine\OperationInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class ThumbnailOperation implements OperationInterface
{

	public function supports(FilterInterface $filter, Scope $scope): bool
	{
		return $filter->getName() === 'thumbnail';
	}

	public function operate(ImageInterface $image, FilterInterface $filter): void
	{
		$image->resize(new Box(15, 15));
	}

}
