<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Filter\Operation\OperationInterface;

final class CompositeFilter implements FilterInterface
{

	private string $name;

	/** @var OperationInterface[] */
	private array $operations = [];

	public function __construct(string $name, OperationInterface|FilterInterface ...$operations)
	{
		$this->name = $name;

		foreach ($operations as $operation) {
			if ($operation instanceof OperationInterface) {
				$this->operations[] = $operation;
			} else {
				$this->operations = array_merge($this->operations, $operation->getOperations());
			}
		}
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier($this->name);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOperations(): array
	{
		return $this->operations;
	}

}
