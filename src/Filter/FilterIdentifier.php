<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

final class FilterIdentifier
{

	private string $name;

	/** @var array<int, mixed> */
	private array $arguments;

	/**
	 * @param array<int, mixed> $arguments
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
	 * @return array<int, mixed>
	 */
	public function getArguments(): array
	{
		return $this->arguments;
	}

}
