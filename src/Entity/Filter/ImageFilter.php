<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity\Filter;

class ImageFilter
{

	/**
	 * @param mixed[] $options
	 */
	public function __construct(
		private string $name,
		private array $options = [],
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
	public function getOptions(): array
	{
		return $this->options;
	}

}
