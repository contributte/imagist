<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\NetteImage;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;

interface NetteOperationRegistryInterface
{

	public function add(NetteOperationInterface $operation): void;

	public function get(FilterInterface $filter, Scope $scope): ?NetteOperationInterface;

}
