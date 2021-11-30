<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Entity\ImageInterface;

interface FilterNormalizerInterface
{

	/**
	 * @return mixed[]
	 */
	public function normalize(ImageInterface $image, ContextInterface $context): array;

}
