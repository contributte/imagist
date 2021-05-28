<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet\Normalizer\Config;

use Contributte\Imagist\Bridge\Gumlet\GumletBuilder;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Filter\FilterInterface;

interface GumletConfigNormalizerInterface
{

	public function getName(): string;

	/**
	 * @param mixed[] $options
	 * @param mixed[] $arguments
	 */
	public function normalize(
		GumletBuilder $builder,
		FilterInterface $filter,
		ImageInterface $image,
		array $options,
		array $arguments
	): void;

}
