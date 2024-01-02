<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Operation;

use Contributte\Imagist\Filter\Operation\Exception\UnusedOperationsException;

final class OperationCollection
{

	/** @var OperationInterface[] */
	private array $operations;

	/**
	 * @param OperationInterface[] $operations
	 */
	public function __construct(array $operations)
	{
		$this->operations = $operations;
	}

	/**
	 * @template T of OperationInterface
	 * @param class-string<T> $className
	 * @return T|null
	 */
	public function get(string $className): ?OperationInterface
	{
		$return = null;
		foreach ($this->operations as $i => $operation) {
			if ($operation instanceof $className) {
				$return = $operation;

				unset($this->operations[$i]);
			}
		}

		return $return;
	}

	/**
	 * @param class-string $className
	 */
	public function remove(string $className): void
	{
		foreach ($this->operations as $i => $operation) {
			if ($operation instanceof $className) {
				unset($this->operations[$i]);
			}
		}
	}

	public function isEmpty(): bool
	{
		return !$this->operations;
	}

	public function validate(): void
	{
		$reports = [];
		foreach ($this->operations as $operation) {
			if (!$operation instanceof SilentOperationInterface) {
				$reports[] = $operation::class;
			}
		}

		if ($reports) {
			throw new UnusedOperationsException(
				sprintf('Image has these unused operations: %s', implode(', ', $reports))
			);
		}
	}

}
