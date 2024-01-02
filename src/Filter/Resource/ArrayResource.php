<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Resource;

final class ArrayResource
{

	/** @var mixed[] */
	private array $array = [];

	/**
	 * @param mixed[] $merge
	 */
	public function merge(array $merge): self
	{
		$this->array = array_merge($this->array, $merge);

		return $this;
	}

	public function add(int|string $key, mixed $value): self
	{
		$this->array[$key] = $value;

		return $this;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return $this->array;
	}

}
