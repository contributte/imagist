<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Bridge\Imagine;

use Contributte\Imagist\Bridge\Imagine\AbstractImagineFilter;
use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Entity\Filter\ImageFilter;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

final class ThumbnailOperation extends AbstractImagineFilter
{

	protected function _supports(ImageInterface $source, ImageFilter $filter, ContextImageAware $context): bool
	{
		return $filter->getName() === 'thumbnail';
	}

	protected function _operate(ImageInterface $source, ImageFilter $filter, ContextImageAware $context): void
	{
		$source->resize(new Box(15, 15));
	}

}
