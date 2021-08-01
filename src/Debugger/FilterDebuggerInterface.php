<?php declare(strict_types = 1);

namespace Contributte\Imagist\Debugger;

interface FilterDebuggerInterface
{

	public function add(DebugFilterObject $object): self;

	public function addProvider(FilterDebuggerProviderInterface $provider): self;

	/**
	 * @return DebugFilterObject[]
	 */
	public function getAll(): array;

}
