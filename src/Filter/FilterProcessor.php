<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Filter\Operation\OperationCollection;
use Contributte\Imagist\Filter\Operation\OperationProcessorInterface;
use Contributte\Imagist\Filter\Resource\ResourceFactoryInterface;

final class FilterProcessor implements FilterProcessorInterface
{

	private ResourceFactoryInterface $resourceFactory;

	/** @var OperationProcessorInterface[] */
	private array $processors;

	/**
	 * @param OperationProcessorInterface[] $processors
	 */
	public function __construct(ResourceFactoryInterface $resourceFactory, array $processors)
	{
		$this->resourceFactory = $resourceFactory;
		$this->processors = $processors;
	}

	public function process(FileInterface $target, FileInterface $source, ContextInterface $context): string
	{
		$filter = $target->getImage()->getFilter();
		if (!$filter) {
			return $source->getContent();
		}

		$collection = new OperationCollection($filter->getOperations());
		if ($collection->isEmpty()) {
			return $source->getContent();
		}

		$resource = $this->resourceFactory->create($source, $context);
		foreach ($this->processors as $processor) {
			$processor->process($resource, $collection, $context);

			if ($collection->isEmpty()) {
				break;
			}
		}

		$collection->validate();

		return $this->resourceFactory->toString($resource, $source, $context);
	}

}
