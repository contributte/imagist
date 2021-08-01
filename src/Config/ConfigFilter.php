<?php declare(strict_types = 1);

namespace Contributte\Imagist\Config;

final class ConfigFilter
{

	/**
	 * @param ConfigFilterOperation[] $operations
	 */
	public function __construct(
		private string $name,
		private array $operations,
	)
	{
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
