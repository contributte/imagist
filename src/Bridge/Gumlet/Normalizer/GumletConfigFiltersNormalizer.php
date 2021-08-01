<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet\Normalizer;

use Contributte\Imagist\Bridge\Gumlet\GumletBuilder;
use Contributte\Imagist\Config\ConfigFilterCollection;
use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Filter\AbstractFilterNormalizerMethodMapping;
use LogicException;

class GumletConfigFiltersNormalizer extends AbstractFilterNormalizerMethodMapping
{

	public function __construct(
		private ConfigFilterCollection $collection,
	)
	{
	}

	protected function getMapping(): array
	{
		$mapping = [];
		foreach ($this->collection->getFilters() as $filter) {
			$mapping[$filter->getName()] = [$this, '_invoke'];
		}

		return $mapping;
	}

	/**
	 * @return mixed[]
	 */
	public function _invoke(ContextImageAware $context): array
	{
		$filter = $context->getImage()->getFilter();
		if (!$filter) {
			return []; // unexpected behavior
		}

		$builder = new GumletBuilder();

		$filter = $this->collection->getFilter($filter->getName());
		foreach ($filter->getOperations() as $operation) {
			$method = $operation->getName();
			if (!method_exists($this, $method)) {
				throw new LogicException(
					sprintf('Class %s does not support config operation %s.', self::class, $operation->getName())
				);
			}

			$callback = [$this, $method];
			if (is_callable($callback)) {
				$callback($builder, ...$operation->getArguments());
			}
		}

		return $builder->build();
	}

	protected function resize(GumletBuilder $builder, mixed ...$arguments): void
	{
		[$width, $height, $mode] = array_replace([
			null,
			null,
			null,
		], $arguments);

		$builder->resize($width, $height, $mode);
	}

	protected function crop(GumletBuilder $builder, string $mode): void
	{
		$builder->crop($mode);
	}

}
