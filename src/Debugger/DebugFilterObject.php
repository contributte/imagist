<?php declare(strict_types = 1);

namespace Contributte\Imagist\Debugger;

final class DebugFilterObject
{

	private string $name;

	/** @var DebugFilterOperationObject[] */
	private array $operations;

	/**
	 * @param DebugFilterOperationObject[] $operations
	 */
	public function __construct(string $name, array $operations)
	{
		$this->name = $name;
		$this->operations = $operations;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return DebugFilterOperationObject[]
	 */
	public function getOperations(): array
	{
		return $this->operations;
	}

}
