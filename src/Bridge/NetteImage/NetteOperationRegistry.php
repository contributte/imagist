<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\NetteImage;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;

final class NetteOperationRegistry implements NetteOperationRegistryInterface
{

	/** @var NetteOperationInterface[] */
	private array $operations = [];

	public function add(NetteOperationInterface $operation): void
	{
		$this->operations[] = $operation;
	}

	public function get(FilterInterface $filter, Scope $scope): ?NetteOperationInterface
	{
		foreach ($this->operations as $operation) {
			if ($operation->supports($filter, $scope)) {
				return $operation;
			}
		}

		return null;
	}

}
