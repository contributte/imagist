<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Filter\FilterInterface;
use Nette\Utils\Image;

abstract class AbstractNetteFilter implements FilterInterface
{

	final public function supports(object $source, ImageFilter $filter, ContextImageAware $context): bool
	{
		return $source instanceof Image && $this->_supports($source, $filter, $context);
	}

	final public function operate(object $source, ImageFilter $filter, ContextImageAware $context): void
	{
		assert($source instanceof Image);

		$this->_operate($source, $filter, $context);
	}

	abstract protected function _supports(Image $source, ImageFilter $filter, ContextImageAware $context): bool;

	abstract protected function _operate(Image $source, ImageFilter $filter, ContextImageAware $context): void;

}
