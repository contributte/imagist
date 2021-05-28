<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet\Normalizer\Config\Normalizer;

use Contributte\Imagist\Bridge\Gumlet\GumletBuilder;
use Contributte\Imagist\Bridge\Gumlet\Normalizer\Config\GumletConfigNormalizerInterface;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Filter\FilterInterface;

final class CropConfigNormalizer implements GumletConfigNormalizerInterface
{

	public function getName(): string
	{
		return 'crop';
	}

	public function normalize(
		GumletBuilder $builder,
		FilterInterface $filter,
		ImageInterface $image,
		array $options,
		array $arguments
	): void
	{
		[$mode] = $arguments;

		$builder->crop($mode);
	}

}
