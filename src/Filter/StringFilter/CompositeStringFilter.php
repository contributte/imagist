<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\StringFilter;

use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Filter\Operation\OperationInterface;
use LogicException;

/**
 * @internal
 */
final class CompositeStringFilter implements FilterInterface
{

	/** @var OperationInterface[] */
	private array $operations = [];

	public function __construct(OperationInterface|FilterInterface ...$operations)
	{
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
		throw new LogicException('CompositeStringFilter is incorrectly used.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOperations(): array
	{
		return $this->operations;
	}

}
