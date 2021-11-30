<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

/**
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
	 * @return T
	 */
	public function create(array $arguments): FilterInterface
	{
		$filter = new ($this->className)(...$arguments);
		if ($this->name) {
			return new CompositeFilter($this->name, );
		}
		return new ($this->className)(...$arguments);
	}

}
