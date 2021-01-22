<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Exceptions\FilterNormalizerNotFoundException;

final class FilterNormalizerCollection implements FilterNormalizerCollectionInterface
{

	/** @var FilterNormalizerInterface[] */
	private array $normalizers = [];

	public function add(FilterNormalizerInterface $normalizer): void
	{
		$this->normalizers[] = $normalizer;
	}

	/**
	 * @inheritDoc
	 */
	public function normalize(ImageInterface $image, array $options = []): ?array
	{
		$filter = $image->getFilter();
		if (!$filter) {
			return null;
		}

		foreach ($this->normalizers as $normalizer) {
			if ($normalizer->supports($filter, $image, $options)) {
				return $normalizer->normalize($filter, $image, $options);
			}
		}

		throw new FilterNormalizerNotFoundException(
			sprintf('Filter normalizer not found for filter %s and image %s', $filter->getName(), $image->getId())
		);
	}

}
