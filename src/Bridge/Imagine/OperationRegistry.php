<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Filter\FilterInterface;

final class OperationRegistry implements OperationRegistryInterface
{

	/** @var OperationInterface[] */
	private array $operations = [];

	public function add(OperationInterface $operation): void
	{
		$this->operations[] = $operation;
	}

	public function get(FilterInterface $filter, ContextImageAware $context): ?OperationInterface
	{
		foreach ($this->operations as $operation) {
			if ($operation->supports($filter, $context)) {
				return $operation;
			}
		}

		return null;
	}

}
