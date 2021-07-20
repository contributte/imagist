<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Entity;

use Contributte\Imagist\Bridge\Nette\Uploader\FileUploadUploader;
use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\StorableImageInterface;
use Contributte\Imagist\ImageStorageInterface;

final class UploadControlEntity
{

	private ?StorableImageInterface $value;

	private ?PersistentImageInterface $default;

	private bool $removeAnyway = false;

	public function __construct(?StorableImageInterface $value = null, ?PersistentImageInterface $default = null)
	{
		$this->value = $value;
		$this->default = $default;
	}

	public function getSize(): ?int
	{
		$uploader = $this->value?->getUploader();
		if ($uploader instanceof FileUploadUploader) {
			return $uploader->getFileUpload()->getSize();
		}

		return null;
	}

	public function getError(): ?int
	{
		$uploader = $this->value?->getUploader();
		if ($uploader instanceof FileUploadUploader) {
			return $uploader->getFileUpload()->getError();
		}

		return null;
	}

	public function getDefault(): ?PersistentImageInterface
	{
		return $this->default;
	}

	public function getValue(): ?StorableImageInterface
	{
		return $this->value;
	}

	public function withRemoveAnyway(bool $removeAnyway): self
	{
		$clone = clone $this;
		$clone->removeAnyway = $removeAnyway;

		return $clone;
	}

	public function toRemove(): bool
	{
		if (!$this->default) {
			return false;
		}

		return $this->value || $this->removeAnyway;
	}

	public function toPersist(): bool
	{
		return (bool) $this->value;
	}

	public function resolve(ImageStorageInterface $imageStorage): ?PersistentImageInterface
	{
		$value = $this->default;
		if ($this->toRemove()) {
			// @phpstan-ignore-next-line
			$imageStorage->remove($this->default);

			$value = null;
		}

		if ($this->toPersist()) {
			// @phpstan-ignore-next-line
			return $imageStorage->persist($this->value);
		}

		return $value;
	}

	public function withDefault(?PersistentImageInterface $default = null): self
	{
		$clone = clone $this;

		if ($default instanceof EmptyImageInterface) {
			$default = null;
		}

		$clone->default = $default;

		return $clone;
	}

	public function withValue(?StorableImageInterface $value = null): self
	{
		$clone = clone $this;

		if ($value instanceof EmptyImageInterface) {
			$value = null;
		}

		$clone->value = $value;

		return $clone;
	}

}
