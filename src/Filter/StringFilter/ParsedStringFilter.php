<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\StringFilter;

use Contributte\Imagist\Filter\FilterIdentifier;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Filter\Operation\OperationInterface;

/**
 * @internal Use only in classes which implement StringFilterCollectionInterface
 */
final class ParsedStringFilter implements FilterInterface
{

	private string $name;

	/** @var mixed[] */
	private array $arguments;

	/** @var OperationInterface[] */
	private array $operations;

	/**
	 * @param mixed[] $arguments
	 * @param OperationInterface[] $operations
	 */
	public function __construct(string $name, array $arguments, array $operations)
	{
		$this->name = $name;
		$this->arguments = $arguments;
		$this->operations = $operations;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier($this->name, $this->arguments);
	}

	public function getOperations(): array
	{
		return $this->operations;
	}

}
