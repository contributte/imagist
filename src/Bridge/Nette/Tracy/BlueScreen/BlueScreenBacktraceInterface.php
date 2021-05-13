<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Tracy\BlueScreen;

interface BlueScreenBacktraceInterface
{

	public function getBackTrace(): array;

}
