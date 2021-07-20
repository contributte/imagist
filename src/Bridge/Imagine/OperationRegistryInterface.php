<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Filter\FilterInterface;

interface OperationRegistryInterface
{

	public function add(OperationInterface $operation): void;

	public function get(FilterInterface $filter, ContextImageAware $context): ?OperationInterface;

}
