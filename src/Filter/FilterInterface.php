<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Entity\Filter\ImageFilter;

interface FilterInterface
{

	public function supports(object $source, ImageFilter $filter, ContextImageAware $context): bool;

	public function operate(object $source, ImageFilter $filter, ContextImageAware $context): void;

}
