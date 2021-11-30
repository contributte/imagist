<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter;

use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Filter\Operation\CropOperation;
use Contributte\Imagist\Filter\Operation\OperationCollection;
use Contributte\Imagist\Filter\Operation\OperationProcessorInterface;
use Contributte\Imagist\Filter\Operation\QualityOperation;
use Contributte\Imagist\Filter\Operation\ResizeOperation;
use Nette\Utils\Image;

final class NetteOperationProcessor implements OperationProcessorInterface
{

	public function process(object $resource, OperationCollection $collection, ContextInterface $context): void
	{
		if (!$resource instanceof Image) {
			return;
		}

		if ($resize = $collection->get(ResizeOperation::class)) {
			$resource->resize($resize->getWidth(), $resize->getHeight(), $this->getIntMode($resize->getMode()));
		}

		if ($crop = $collection->get(CropOperation::class)) {
			$resource->crop($crop->getLeft(), $crop->getTop(), $crop->getWidth(), $crop->getHeight());
		}

		if ($quality = $collection->get(QualityOperation::class)) {
			$context->set(NetteResourceFactory::QUALITY_CONTEXT, $quality->getQuality());
		}
	}

	private function getIntMode(?string $mode): int
	{
		switch ($mode) {
			case 'fill':
				return Image::FILL;
			case 'exact':
				return Image::EXACT;
			case 'shrink_only':
				return Image::SHRINK_ONLY;
			case 'stretch':
				return Image::STRETCH;
			default:
				return Image::FIT;
		}
	}

}
