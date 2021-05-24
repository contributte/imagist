<?php declare(strict_types = 1);

namespace Contributte\Imagist\Config;

final class ConfigFilterStack
{

	private string $name;

	/** @var ConfigFilter[] $configFilters */
	private array $configFilters;

	/**
	 * @param ConfigFilter[] $configFilters
	 */
	public function __construct(string $name, array $configFilters)
	{
		$this->name = $name;
		$this->configFilters = $configFilters;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return ConfigFilter[]
	 */
	public function getConfigFilters(): array
	{
		return $this->configFilters;
	}

}
