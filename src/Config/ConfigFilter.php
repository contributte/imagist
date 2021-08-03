<?php declare(strict_types = 1);

namespace Contributte\Imagist\Config;

final class ConfigFilter
{

	private string $name;

	/** @var ConfigFilterOperation[] */
	private array $operations;

	/**
	 * @param ConfigFilterOperation[] $operations
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
	 * @return ConfigFilterOperation[]
	 */
	public function getOperations(): array
	{
		return $this->operations;
	}

}
