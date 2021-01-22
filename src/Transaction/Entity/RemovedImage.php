<?php declare(strict_types = 1);

namespace Contributte\Imagist\Transaction\Entity;

use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\PromisedImageInterface;

/**
 * @internal
 */
final class RemovedImage
{

	private PersistentImageInterface $source;

	private PromisedImageInterface $promisedImage;

	private string $content;

	private bool $removed = false;

	public function __construct(
		PersistentImageInterface $source,
		PromisedImageInterface $promisedImage,
		string $content
	)
	{
		$this->source = $source;
		$this->promisedImage = $promisedImage;
		$this->content = $content;
	}

	public function setRemoved(): void
	{
		$this->removed = true;
	}

	public function isRemoved(): bool
	{
		return $this->removed;
	}

	public function getSource(): PersistentImageInterface
	{
		return $this->source;
	}

	public function getPromisedImage(): PromisedImageInterface
	{
		return $this->promisedImage;
	}

	public function getContent(): string
	{
		return $this->content;
	}

}
