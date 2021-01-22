<?php declare(strict_types = 1);

namespace Contributte\Imagist\File;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\MimeType\ImageMimeType;

interface FileInterface
{

	public function getImage(): ImageInterface;

	public function exists(): bool;

	public function delete(): void;

	public function getContent(): string;

	/**
	 * @param mixed[] $config
	 */
	public function setContent(string $content, array $config = []): void;

	public function getPath(): string;

	public function getMimeType(): ImageMimeType;

}
