<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Context\ContextInterface;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Exceptions\FilterNormalizerNotFoundException;

class FilterNormalizerProcessor implements FilterNormalizerProcessorInterface
{

	/** @var FilterNormalizerInterface[] */
	private array $operations;

	/**
	 * @param FilterNormalizerInterface[] $operations
	 */
	public function __construct(array $operations)
	{
		$this->operations = $operations;
	}
	/**
	 * @return $this
	 */
	public function addOperation(FilterNormalizerInterface $operation)
	{
		$this->operations[] = $operation;

		return $this;
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

		$context = new ContextImageAware($image, $context);
		foreach ($this->operations as $normalizer) {
			if ($normalizer->supports($filter, $context)) {
				return $normalizer->normalize($filter, $context);
			}
		}

		throw new FilterNormalizerNotFoundException(
			sprintf('Filter normalizer not found for filter %s and image %s', $filter->getName(), $image->getId())
		);
	}

}
