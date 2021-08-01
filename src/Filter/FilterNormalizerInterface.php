<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Entity\Filter\ImageFilter;

interface FilterNormalizerInterface
{

	public function supports(ImageFilter $filter, ContextImageAware $context): bool;

	/**
	 * @return mixed[]
	 */
	public function normalize(ImageFilter $filter, ContextImageAware $context): array;

}
