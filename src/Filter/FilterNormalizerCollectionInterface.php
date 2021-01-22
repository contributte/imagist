<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Entity\ImageInterface;

interface FilterNormalizerCollectionInterface
{

	public function add(FilterNormalizerInterface $normalizer): void;

	/**
	 * @param mixed[] $options
	 * @return mixed[]|null
	 */
	public function normalize(ImageInterface $image, array $options = []): ?array;

}
