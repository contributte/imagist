<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Filter\AbstractFilterProcessor;
use Contributte\Imagist\Filter\FilterInterface;
use Imagine\Gd\Imagine as GdImagine;
use Imagine\Gmagick\Imagine as GmagickImagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Imagick\Imagine;
use RuntimeException;

final class ImagineFilterProcessor extends AbstractFilterProcessor
{

	private ImagineInterface $imagine;

	/**
	 * @param FilterInterface[] $operations
	 */
	public function __construct(array $operations, ?ImagineInterface $imagine = null)
	{
		parent::__construct($operations);

		$this->imagine = $imagine ?? $this->createImagine();
	}

	protected function createImagine(): ImagineInterface
	{
		if (extension_loaded('imagick')) {
			return new Imagine();
		}

		if (extension_loaded('gd')) {
			return new GdImagine();
		}

		if (extension_loaded('gmagick')) {
			return new GmagickImagine();
		}

		throw new RuntimeException('PHP extension not found, need imagick or gd or gmagick');
	}

	protected function createImageInstance(FileInterface $source): ImageInterface
	{
		return $this->imagine->load($source->getContent());
	}

	protected function imageInstanceToString(object $image, FileInterface $source, ContextImageAware $context): string
	{
		assert($image instanceof ImageInterface);

		return $image->get($source->getMimeType()->toSuffix());
	}

}
