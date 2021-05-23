<?php declare(strict_types = 1);

namespace Contributte\Imagist\Uploader;

interface UploaderFilePathAwareInterface
{

	public function getFilePath(): string;

}
