<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Uploader;

use Contributte\Imagist\Bridge\Nette\Exceptions\InvalidArgumentException;
use Contributte\Imagist\Bridge\Nette\Exceptions\InvalidFileUploadException;
use Contributte\Imagist\Uploader\UploaderFilePathAwareInterface;
use Contributte\Imagist\Uploader\UploaderInterface;
use Nette\Http\FileUpload;

final class FileUploadUploader implements UploaderInterface, UploaderFilePathAwareInterface
{

	private FileUpload $fileUpload;

	public function __construct(FileUpload $fileUpload)
	{
		if (!$fileUpload->isOk()) {
			throw new InvalidArgumentException('Passed file is not ok');
		}

		if (!$fileUpload->isImage()) {
			throw new InvalidArgumentException('Passed file is not an image');
		}

		$this->fileUpload = $fileUpload;
	}

	public function getContent(): string
	{
		$content = $this->fileUpload->getContents();

		if ($content === null) {
			throw new InvalidFileUploadException(
				sprintf('Cannot get content from %s', $this->fileUpload->getSanitizedName())
			);
		}

		return $content;
	}

	public function getFilePath(): string
	{
		return $this->fileUpload->getTemporaryFile();
	}

}
