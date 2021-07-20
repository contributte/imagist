<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Filter\FilterInterface;
use Imagine\Image\ImageInterface;

interface OperationInterface
{

	public function supports(FilterInterface $filter, ContextImageAware $context): bool;

	public function operate(
		ImageInterface $image,
		FilterInterface $filter,
		ContextImageAware $context
	): void;

}
