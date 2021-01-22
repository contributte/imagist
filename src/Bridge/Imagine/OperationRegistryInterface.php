<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;

interface OperationRegistryInterface
{

	public function add(OperationInterface $operation): void;

	public function get(FilterInterface $filter, Scope $scope): ?OperationInterface;

}
