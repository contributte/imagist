<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Entity\Filter\ImageFilter;

abstract class AbstractFilterNormalizerMethodMapping implements FilterNormalizerInterface
{

	/**
	 * @var array<string, callable>
	 * @phpstan-var array<string, callable(ContextImageAware $context): array>
	 */
	private array $cache;

	/**
	 * @phpstan-return array<string, callable(ContextImageAware $context): array>
	 * @return array<string, callable> filter => method
	 */
	abstract protected function getMapping(): array;

	final public function supports(ImageFilter $filter, ContextImageAware $context): bool
	{
		return isset($this->getCache()[$filter->getName()]);
	}

	final public function normalize(ImageFilter $filter, ContextImageAware $context): array
	{
		$callback = $this->getCache()[$filter->getName()];

		return $callback($context);
	}

	/**
	 * @phpstan-return array<string, callable(ContextImageAware $context): array>
	 * @return array<string, string>
	 */
	private function getCache(): array
	{
		if (!isset($this->cache)) {
			$this->cache = $this->getMapping();
		}

		return $this->cache;
	}

}
