<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy\Dto;

final class DebugFilterDto
{

	private string $filterName;

	/** @var string[] */
	private array $operations;

	/**
	 * @param string[] $operations
	 */
	public function __construct(string $filterName, array $operations)
	{
		$this->filterName = $filterName;
		$this->operations = $operations;
	}

	public function getFilterName(): string
	{
		return $this->filterName;
	}

	/**
	 * @return string[]
	 */
	public function getOperations(): array
	{
		return $this->operations;
	}

}
