<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Filter\Resource\ResourceFactoryInterface;
use Imagine\Gd\Imagine as GdImagine;
use Imagine\Gmagick\Imagine as GmagickImagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Imagick\Imagine;
use RuntimeException;

final class ImagineResourceFactory implements ResourceFactoryInterface
{

	private ImagineInterface $imagine;

	public function __construct()
	{
		$this->imagine = $this->createImagine();
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

	public function create(FileInterface $source, ContextInterface $context): ImageInterface
	{
		return $this->imagine->load($source->getContent());
	}

	public function toString(object $resource, FileInterface $source, ContextInterface $context): string
	{
		assert($resource instanceof ImageInterface);

		return $resource->get($source->getMimeType()->toSuffix());
	}

}
