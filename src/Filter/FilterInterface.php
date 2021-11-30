<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Filter\Operation\OperationInterface;

interface FilterInterface
{

	public function getIdentifier(): FilterIdentifier;

	/**
	 * @return OperationInterface[]
	 */
	public function getOperations(): array;

}
