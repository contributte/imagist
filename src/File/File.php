<?php declare(strict_types = 1);

namespace Contributte\Imagist\File;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\StorableImageInterface;
use Contributte\Imagist\Filesystem\FilesystemInterface;
use Contributte\Imagist\MimeType\ImageMimeType;
use Contributte\Imagist\PathInfo\PathInfoInterface;
use finfo;

class File implements FileInterface
{

	private ImageInterface $image;

	private FilesystemInterface $filesystem;

	private PathInfoInterface $pathInfo;

	public function __construct(ImageInterface $image, FilesystemInterface $filesystem, PathInfoInterface $pathInfo)
	{
		$this->image = $image;
		$this->filesystem = $filesystem;
		$this->pathInfo = $pathInfo;
	}

	public function getImage(): ImageInterface
	{
		return $this->image;
	}

	public function getContent(): string
	{
		if ($this->image instanceof StorableImageInterface) {
			return $this->image->getUploader()->getContent();
		}

		return $this->filesystem->read($this->pathInfo);
	}

	/**
	 * @param mixed[] $config
	 */
	public function setContent(string $content, array $config = []): void
	{
		$this->filesystem->putWithMkdir($this->pathInfo, $content, $config);
	}

	public function getMimeType(): ImageMimeType
	{
		if ($this->image instanceof StorableImageInterface) {
			$finfo = new finfo(FILEINFO_MIME_TYPE);

			$mimeType = $finfo->buffer($this->getContent());
			$mimeType = $mimeType === false ? null : $mimeType;
		} else {
			$mimeType = $this->filesystem->mimeType($this->pathInfo);
		}

		return new ImageMimeType($mimeType ?? 'unknown');
	}

	public function exists(): bool
	{
		return $this->filesystem->exists($this->pathInfo);
	}

	public function getPath(): string
	{
		return $this->pathInfo->toString();
	}

	public function delete(): void
	{
		$this->filesystem->delete($this->pathInfo);
	}

}
