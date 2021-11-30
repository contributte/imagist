<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Filter\Operation\OperationCollection;
use Contributte\Imagist\Filter\Operation\OperationProcessorInterface;
use Contributte\Imagist\Filter\Resource\ArrayResource;

class FilterNormalizer implements FilterNormalizerInterface
{

	/** @var OperationProcessorInterface[] */
	private array $processors;

	/**
	 * @param OperationProcessorInterface[] $operations
	 */
	public function __construct(array $operations)
	{
		$this->processors = $operations;
	}

	/**
	 * @inheritDoc
	 */
	public function normalize(ImageInterface $image, ContextInterface $context): array
	{
		$filter = $image->getFilter();
		if (!$filter) {
			return [];
		}

		$collection = new OperationCollection($filter->getOperations());
		if ($collection->isEmpty()) {
			return [];
		}

		$resource = new ArrayResource();
		foreach ($this->processors as $processor) {
			$processor->process($resource, $collection, $context);

			if ($collection->isEmpty()) {
				break;
			}
		}

		$collection->validate();

		return $resource->toArray();
	}

}
