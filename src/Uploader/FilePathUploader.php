<?php declare(strict_types = 1);

namespace Contributte\Imagist\Uploader;

use Contributte\Imagist\Exceptions\CannotSaveFileException;
use Contributte\Imagist\Exceptions\InvalidArgumentException;

class FilePathUploader implements UploaderInterface
{

	private string $filePath;

	public function __construct(string $filePath)
	{
		if (!is_file($filePath)) {
			throw new InvalidArgumentException(sprintf('"%s" is not a file or not exists', $filePath));
		}

		$this->filePath = $filePath;
	}

	public function getContent(): string
	{
		if (($content = @file_get_contents($this->filePath)) === false) {
			throw new CannotSaveFileException(sprintf('Cannot save "%s"', $this->filePath));
		}

		return $content;
	}

}
