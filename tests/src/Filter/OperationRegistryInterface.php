<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Filter;

use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Scope\Scope;

interface OperationRegistryInterface
{

	public function add(OperationInterface $operation): void;

	public function get(ImageFilter $filter, Scope $scope): ?OperationInterface;

}
