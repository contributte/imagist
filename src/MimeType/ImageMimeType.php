<?php declare(strict_types = 1);

namespace Contributte\Imagist\MimeType;

use Contributte\Imagist\Exceptions\ImageException;

final class ImageMimeType
{

	private const MIME_TYPES = [
		'image/jpeg' => true,
		'image/gif' => true,
		'image/png' => true,
		'image/webp' => true,
	];

	private const SUFFIXES = [
		'image/jpeg' => 'jpg',
		'image/gif' => 'gif',
		'image/png' => 'png',
		'image/webp' => 'webp',
	];

	private string $mimeType;

	public function __construct(string $mimeType)
	{
		$this->mimeType = $mimeType;
	}

	public function isImage(): bool
	{
		return isset(self::MIME_TYPES[$this->mimeType]);
	}

	public function toSuffix(): string
	{
		$this->assertIsImage();

		return self::SUFFIXES[$this->mimeType];
	}

	public function getMimeType(): string
	{
		return $this->mimeType;
	}

	protected function assertIsImage(): void
	{
		if (!$this->isImage()) {
			throw new ImageException(sprintf('Mimetype "%s" is not an image', $this->mimeType));
		}
	}

}
