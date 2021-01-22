<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\FileNameResolvers;

use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Resolver\FileNameResolverInterface;
use Ramsey\Uuid\Uuid;

final class UuidFileNameResolver implements FileNameResolverInterface
{

	public function resolve(FileInterface $file): string
	{
		$suffix = $file->getImage()->getSuffix();

		return Uuid::uuid4()->toString() . ($suffix ? '.' . $suffix : '');
	}

}
