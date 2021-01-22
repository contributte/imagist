<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Symfony\Uploader;

use Contributte\Imagist\Uploader\UploaderInterface;
use LogicException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedFileUploader implements UploaderInterface
{

	private UploadedFile $uploadedFile;

	public function __construct(UploadedFile $uploadedFile)
	{
		$this->uploadedFile = $uploadedFile;
	}

	public function getContent(): string
	{
		$content = file_get_contents($this->uploadedFile->getPathname());
		if ($content === false) {
			throw new LogicException(sprintf('Cannot get content from file %s', $this->uploadedFile->getPathname()));
		}

		return $content;
	}

}
