<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity\Filter;

class ImageFilter
{

	private string $name;

	/** @var mixed[] */
	private array $options = [];

	/**
	 * @param mixed[] $options
	 */
	public function __construct(string $name, array $options = [])
	{
		$this->name = $name;
		$this->options = $options;
	}
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return mixed[]
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

}
