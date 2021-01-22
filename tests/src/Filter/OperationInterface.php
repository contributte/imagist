<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Filter;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;
use Nette\Utils\Image;

interface OperationInterface
{

	public function supports(FilterInterface $filter, Scope $scope): bool;

	public function operate(Image $image, FilterInterface $filter): void;

}
