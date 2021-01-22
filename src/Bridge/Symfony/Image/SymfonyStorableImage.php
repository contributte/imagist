<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Symfony\Image;

use Contributte\Imagist\Bridge\Symfony\Uploader\UploadedFileUploader;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\Scope\Scope;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SymfonyStorableImage extends StorableImage
{

	public function __construct(UploadedFile $file, ?string $name = null, ?Scope $scope = null)
	{
		parent::__construct(new UploadedFileUploader($file), $name ?? $file->getBasename(), $scope);
	}

}
