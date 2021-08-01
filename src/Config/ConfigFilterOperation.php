<?php declare(strict_types = 1);

namespace Contributte\Imagist\Config;

final class ConfigFilterOperation
{

	private string $name;

	/** @var mixed[] */
	private array $arguments;

	private ?string $description;

	/**
	 * @param mixed[] $arguments
	 */
	public function __construct(string $name, array $arguments = [], ?string $description = null)
	{
		$this->name = $name;
		$this->arguments = $arguments;
		$this->description = $description;
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
