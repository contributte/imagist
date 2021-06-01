<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet\Normalizer\Config;

use Contributte\Imagist\Bridge\Gumlet\GumletBuilder;
use Contributte\Imagist\Config\ConfigFilterStack;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Exceptions\UnexpectedErrorException;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Filter\FilterNormalizerInterface;
use InvalidArgumentException;

final class GumletConfigNormalizerRegistry implements FilterNormalizerInterface
{

	/** @var ConfigFilterStack[] */
	protected array $configFilterStacks = [];

	/** @var GumletConfigNormalizerInterface[] */
	protected array $normalizers = [];

	public function addConfigFilterStack(ConfigFilterStack $configFilterStack): void
	{
		$this->configFilterStacks[$configFilterStack->getName()] = $configFilterStack;
	}

	public function addNormalizer(GumletConfigNormalizerInterface $normalizer): void
	{
		$this->normalizers[$normalizer->getName()] = $normalizer;
	}

	public function supports(FilterInterface $filter, ImageInterface $image, array $options): bool
	{
		return isset($options['gumlet']) && isset($this->configFilterStacks[$filter->getName()]);
	}

	public function normalize(FilterInterface $filter, ImageInterface $image, array $options): array
	{
		if (!isset($this->configFilterStacks[$filter->getName()])) {
			throw new UnexpectedErrorException();
		}

		$builder = GumletBuilder::create();
		foreach ($this->configFilterStacks[$filter->getName()]->getConfigFilters() as $configFilter) {
			if (!isset($this->normalizers[$configFilter->getName()])) {
				throw new InvalidArgumentException(sprintf('Config normalizer %s not exists.', $configFilter->getName()));
			}

			$normalizer = $this->normalizers[$configFilter->getName()];
			$normalizer->normalize($builder, $filter, $image, $options, $configFilter->getArguments());
		}

		return $builder->build();
	}

}
