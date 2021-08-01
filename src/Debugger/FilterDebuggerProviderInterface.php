<?php declare(strict_types = 1);

namespace Contributte\Imagist\Debugger;

interface FilterDebuggerProviderInterface
{

	/**
	 * @return DebugFilterObject[]
	 */
	public function provideDebugFilters(): iterable;

}
