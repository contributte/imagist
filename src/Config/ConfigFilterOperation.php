<?php declare(strict_types = 1);

namespace Contributte\Imagist\Config;

final class ConfigFilterOperation
{

	/**
	 * @param mixed[] $arguments
	 */
	public function __construct(
		private string $name,
		private array $arguments = [],
		private ?string $description = null,
	)
	{
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return mixed[]
	 */
	public function getArguments(): array
	{
		return $this->arguments;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

}
