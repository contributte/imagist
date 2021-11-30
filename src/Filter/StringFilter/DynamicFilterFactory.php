<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\StringFilter;

use Contributte\Imagist\Filter\CompositeFilter;
use Contributte\Imagist\Filter\FilterInterface;

/**
 * @internal
 * @template T of FilterInterface
 */
final class DynamicFilterFactory
{

	private ?string $name;

	/**
	 * @var class-string<T>
	 */
	private string $className;

	/**
	 * @param class-string<T> $className
	 */
	public function __construct(string $className, ?string $name = null)
	{
		$this->className = $className;
		$this->name = $name;
	}

	/**
	 * @param mixed[] $arguments
	 * @return T|DecorateDynamicFilter
	 */
	public function create(array $arguments): FilterInterface
	{
		$filter = new ($this->className)(...$arguments);
		if ($this->name) {
			return new DecorateDynamicFilter($this->name, $filter, $arguments);
		}

		return $filter;
	}

}
