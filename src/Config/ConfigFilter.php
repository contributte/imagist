<?php declare(strict_types = 1);

namespace Contributte\Imagist\Config;

final class ConfigFilter
{

	private string $name;

	/** @var mixed[] */
	private array $arguments;

	/**
	 * @param mixed[] $arguments
	 */
	public function __construct(string $name, array $arguments = [])
	{
		$this->name = $name;
		$this->arguments = $arguments;
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

}
