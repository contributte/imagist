<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver;

use Contributte\Imagist\File\FileInterface;

interface FileNameResolverInterface
{

	public function resolve(FileInterface $file): string;

}
