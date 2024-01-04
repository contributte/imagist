<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filesystem;

use League\Flysystem\Local\LocalFilesystemAdapter;

final class LocalFilesystem extends FilesystemAbstract
{

	public function __construct(string $root)
	{
		parent::__construct(new LocalFilesystemAdapter($root));
	}

}
