<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Entity\Filter\ImageFilter;

abstract class AbstractFilterMethodMapping implements FilterInterface
{

	/**
	 * @var array<string, callable>
	 * @phpstan-var array<string, callable(object $source, ContextImageAware $context): void>
	 */
	private array $cache;

	/**
	 * @phpstan-return array<string, callable(object $source, ContextImageAware $context): void>
	 * @return array<string, callable> filter => method
	 */
	abstract protected function getMapping(): array;

	abstract protected function supportsSource(object $source): bool;

	final public function supports(object $source, ImageFilter $filter, ContextImageAware $context): bool
	{
		return $this->supportsSource($source) && isset($this->getCache()[$filter->getName()]);
	}

	final public function operate(object $source, ImageFilter $filter, ContextImageAware $context): void
	{
		$callback = $this->getCache()[$filter->getName()];

		$callback($source, $context);
	}

	/**
	 * @phpstan-return array<string, callable(object $source, ContextImageAware $context): void>
	 * @return array<string, callable>
	 */
	private function getCache(): array
	{
		if (!isset($this->cache)) {
			$this->cache = $this->getMapping();
		}

		return $this->cache;
	}

}
