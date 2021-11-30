<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Operation;

use Contributte\Imagist\Filter\Context\ContextInterface;

interface OperationProcessorInterface
{

	public function process(object $resource, OperationCollection $collection, ContextInterface $context): void;

}
