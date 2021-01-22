<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\FileNameResolvers;

use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Resolver\FileNameResolverInterface;

final class OriginalFileNameResolver implements FileNameResolverInterface
{

	public function resolve(FileInterface $file): string
	{
		return $file->getImage()->getName();
	}

}
