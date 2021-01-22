<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;

final class OperationRegistry implements OperationRegistryInterface
{

	/** @var OperationInterface[] */
	private array $operations = [];

	public function add(OperationInterface $operation): void
	{
		$this->operations[] = $operation;
	}

	public function get(FilterInterface $filter, Scope $scope): ?OperationInterface
	{
		foreach ($this->operations as $operation) {
			if ($operation->supports($filter, $scope)) {
				return $operation;
			}
		}

		return null;
	}

}
