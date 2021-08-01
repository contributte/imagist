<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Filter\FilterInterface;
use Imagine\Image\ImageInterface;

abstract class AbstractImagineFilter implements FilterInterface
{

	final public function supports(object $source, ImageFilter $filter, ContextImageAware $context): bool
	{
		return $source instanceof ImageInterface && $this->_supports($source, $filter, $context);
	}

	final public function operate(object $source, ImageFilter $filter, ContextImageAware $context): void
	{
		assert($source instanceof ImageInterface);

		$this->_operate($source, $filter, $context);
	}

	abstract protected function _supports(ImageInterface $source, ImageFilter $filter, ContextImageAware $context): bool;

	abstract protected function _operate(ImageInterface $source, ImageFilter $filter, ContextImageAware $context): void;

}
