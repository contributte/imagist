<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Operation;

use Contributte\Imagist\Filter\FilterInterface;

abstract class OperationAsFilter implements OperationInterface, FilterInterface
{

	/**
	 * @return OperationInterface[]
	 */
	public function getOperations(): array
	{
		return [$this];
	}

}
