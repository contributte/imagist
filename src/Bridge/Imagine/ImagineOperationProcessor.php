<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Filter\Operation\CropOperation;
use Contributte\Imagist\Filter\Operation\OperationCollection;
use Contributte\Imagist\Filter\Operation\OperationProcessorInterface;
use Contributte\Imagist\Filter\Operation\ResizeOperation;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

final class ImagineOperationProcessor implements OperationProcessorInterface
{

	public function process(object $resource, OperationCollection $collection, ContextInterface $context): void
	{
		if (!$resource instanceof ImageInterface) {
			return;
		}

		if ($crop = $collection->get(CropOperation::class)) {
			$size = $resource->getSize();
			[$x, $y, $width, $height] = $crop->calculate($size->getWidth(), $size->getHeight());

			$resource->crop(new Point($x, $y), new Box($width, $height));
		}

		if ($resize = $collection->get(ResizeOperation::class)) {
			$width = $resize->getWidth();
			$height = $resize->getHeight();

			if (!is_int($width) || !is_int($height) || $resize->getMode() !== null) {
				$size = $resource->getSize();
				[$width, $height] = $resize->calculateWithMode($size->getWidth(), $size->getHeight());
			}

			$resource->resize(new Box($width, $height));
		}
	}

}
