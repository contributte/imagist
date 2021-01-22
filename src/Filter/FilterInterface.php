<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

interface FilterInterface
{

	public function getName(): string;

	/**
	 * @return mixed[]
	 */
	public function getOptions(): array;

}
