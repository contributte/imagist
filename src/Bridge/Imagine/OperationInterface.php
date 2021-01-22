<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;
use Imagine\Image\ImageInterface;

interface OperationInterface
{

	public function supports(FilterInterface $filter, Scope $scope): bool;

	public function operate(ImageInterface $image, FilterInterface $filter): void;

}
