<?php declare(strict_types = 1);

namespace Contributte\Imagist\Debugger;

final class FilterDebugger implements FilterDebuggerInterface
{

	/** @var DebugFilterObject[] */
	private array $objects = [];

	/** @var FilterDebuggerProviderInterface[] */
	private array $providers = [];

	/**
	 * @return $this
	 */
	public function add(DebugFilterObject $object)
	{
		$this->objects[] = $object;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function addProvider(FilterDebuggerProviderInterface $provider)
	{
		$this->providers[] = $provider;

		return $this;
	}

	/**
	 * @return DebugFilterObject[]
	 */
	public function getAll(): array
	{
		foreach ($this->providers as $provider) {
			foreach ($provider->provideDebugFilters() as $object) {
				$this->objects[] = $object;
			}
		}

		$this->providers = [];

		return $this->objects;
	}

}
