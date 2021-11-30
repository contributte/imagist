<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\StringFilter;

use Contributte\Imagist\Filter\FilterInterface;

interface StringFilterCollectionInterface
{

	/**
	 * @param mixed[] $arguments
	 */
	public function get(string $name, array $arguments = []): FilterInterface;

}
