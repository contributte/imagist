<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\StringFilter;

use Contributte\Imagist\Filter\FilterInterface;

/**
 * @internal
 * @template T of FilterInterface
 */
final class DynamicFilterFactory
{

	/** @var class-string<T> */ // phpcs:ignore -- bug
	private string $className;

	/**
	 * @param class-string<T> $className
	 */
	public function __construct(string $className)
	{
		$this->className = $className;
	}

	/**
	 * @param mixed[] $arguments
	 * @return T
	 */
	public function create(array $arguments): FilterInterface
	{
		return new ($this->className)(...$arguments); // phpcs:ignore -- bug
	}

}
