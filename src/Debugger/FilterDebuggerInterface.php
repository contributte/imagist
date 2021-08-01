<?php declare(strict_types = 1);

namespace Contributte\Imagist\Debugger;

interface FilterDebuggerInterface
{

	/**
	 * @return $this
	 */
	public function add(DebugFilterObject $object);

	/**
	 * @return $this
	 */
	public function addProvider(FilterDebuggerProviderInterface $provider);

	/**
	 * @return DebugFilterObject[]
	 */
	public function getAll(): array;

}
