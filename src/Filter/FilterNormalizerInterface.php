<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Entity\ImageInterface;

interface FilterNormalizerInterface
{

	/**
	 * @param mixed[] $options
	 */
	public function supports(FilterInterface $filter, ImageInterface $image, array $options): bool;

	/**
	 * @param mixed[] $options
	 * @return mixed[]
	 */
	public function normalize(FilterInterface $filter, ImageInterface $image, array $options): array;

}
