<?php declare(strict_types = 1);

namespace Contributte\Imagist\Config;

use Contributte\Imagist\Debugger\DebugFilterObject;
use Contributte\Imagist\Debugger\DebugFilterOperationObject;
use Contributte\Imagist\Debugger\FilterDebuggerProviderInterface;

final class ConfigFilterCollection implements FilterDebuggerProviderInterface
{

	/**
	 * @param ConfigFilter[] $filters
	 */
	public function __construct(
		private array $filters = [],
	)
	{
	}

	public function addFilter(ConfigFilter $filter): self
	{
		$this->filters[$filter->getName()] = $filter;

		return $this;
	}

	/**
	 * @return ConfigFilter[]
	 */
	public function getFilters(): array
	{
		return $this->filters;
	}

	public function getFilter(string $name): ConfigFilter
	{
		return $this->filters[$name];
	}

	/**
	 * @internal
	 * @return DebugFilterObject[]
	 */
	public function provideDebugFilters(): iterable
	{
		foreach ($this->filters as $filter) {
			yield new DebugFilterObject($filter->getName(), array_map(
				fn (ConfigFilterOperation $op) => new DebugFilterOperationObject($op->getName(), $op->getArguments()),
				$filter->getOperations()
			));
		}
	}

}
