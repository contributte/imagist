<?php declare(strict_types = 1);

namespace Contributte\Imagist\Database;

use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\PromisedImage;
use Contributte\Imagist\Entity\PromisedImageInterface;
use Contributte\Imagist\Entity\StorableImageInterface;
use Contributte\Imagist\Exceptions\InvalidArgumentException;

final class DatabaseConverter implements DatabaseConverterInterface
{

	private bool $nullable;

	/** @var class-string<PersistentImage> */
	private string $className;

	/**
	 * @param class-string<PersistentImage> $className
	 */
	public function __construct(bool $nullable = true, string $className = PersistentImage::class)
	{
		$this->nullable = $nullable;
		$this->className = $className;
	}

	public function convertToDatabase(?ImageInterface $image): ?string
	{
		if (!$image) {
			return null;
		}

		if ($image instanceof StorableImageInterface) {
			throw new InvalidArgumentException(
				sprintf('Cannot convert %s to database, first persist image and pass the result', $image->getId())
			);
		}

		if ($image instanceof PromisedImageInterface) {
			if ($image->isPending()) {
				if ($image instanceof PromisedImage) {
					throw new InvalidArgumentException(sprintf('Given image "%s" is still pending', PromisedImage::getSourceId($image)));
				}

				throw new InvalidArgumentException('Given image is still pending');
			}

			$image = $image->getResult();
		}

		if ($image instanceof EmptyImageInterface) {
			return null;
		}

		return $image->getId();
	}

	public function convertToPhp(?string $value, ?bool $nullable = null): ?PersistentImageInterface
	{
		if (!$value) {
			if ($nullable === null) {
				$nullable = $this->nullable;
			}

			return $nullable ? null : new EmptyImage();
		}

		return new $this->className($value);
	}

}
