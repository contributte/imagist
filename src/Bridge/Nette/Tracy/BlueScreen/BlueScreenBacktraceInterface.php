<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy\BlueScreen;

interface BlueScreenBacktraceInterface
{

	/**
	 * @return mixed[]
	 */
	public function getBackTrace(): array;

}
