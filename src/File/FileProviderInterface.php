<?php declare(strict_types = 1);

namespace Contributte\Imagist\File;

interface FileProviderInterface
{

	public function provideFile(): FileInterface;

}
