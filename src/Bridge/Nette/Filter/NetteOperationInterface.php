<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;
use Nette\Utils\Image;

interface NetteOperationInterface
{

	public function supports(FilterInterface $filter, Scope $scope): bool;

	public function operate(Image $image, FilterInterface $filter, NetteImageOptions $options): void;

}
