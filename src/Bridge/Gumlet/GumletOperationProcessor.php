<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet;

use Contributte\Imagist\Bridge\Gumlet\Operation\GumletCropModeOperation;
use Contributte\Imagist\Bridge\Gumlet\Operation\GumletFillOperation;
use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Filter\Operation\CropOperation;
use Contributte\Imagist\Filter\Operation\MaskOperation;
use Contributte\Imagist\Filter\Operation\OperationCollection;
use Contributte\Imagist\Filter\Operation\OperationProcessorInterface;
use Contributte\Imagist\Filter\Operation\ResizeOperation;
use Contributte\Imagist\Filter\Resource\ArrayResource;

final class GumletOperationProcessor implements OperationProcessorInterface
{

	private const RESIZE_MODE_MAP = [
		'exact' => 'crop',
	];

	public function process(object $resource, OperationCollection $collection, ContextInterface $context): void
	{
		if (!$resource instanceof ArrayResource) {
			return;
		}

		$builder = new GumletBuilder();

		if ($resize = $collection->get(ResizeOperation::class)) {
			$builder->resize(
				$resize->getWidth(),
				$resize->getHeight(),
				self::RESIZE_MODE_MAP[$resize->getMode()] ?? $resize->getMode()
			);
		}

		if ($mask = $collection->get(MaskOperation::class)) {
			$builder->mask($mask->getMask());
		}

		if ($cropMode = $collection->get(GumletCropModeOperation::class)) {
			$builder->crop($cropMode->getMode());
		}

		if ($crop = $collection->get(CropOperation::class)) {
			$builder->extract($crop->getLeft(), $crop->getTop(), $crop->getWidth(), $crop->getHeight());
		}

		if ($fill = $collection->get(GumletFillOperation::class)) {
			$builder->fill($fill->getFill());
		}

		$resource->merge($builder->build());
	}

}
