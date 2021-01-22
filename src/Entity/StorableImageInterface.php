<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

use Contributte\Imagist\Uploader\UploaderInterface;

interface StorableImageInterface extends ImageInterface
{

	public function getUploader(): UploaderInterface;

	public function close(?string $reason = null): void;

	public function isClosed(): bool;

}
