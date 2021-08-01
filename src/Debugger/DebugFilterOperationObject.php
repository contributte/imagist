<?php declare(strict_types = 1);

namespace Contributte\Imagist\Debugger;

final class DebugFilterOperationObject
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

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return mixed[]
	 */
	public function getArguments(): array
	{
		return $this->arguments;
	}

}
