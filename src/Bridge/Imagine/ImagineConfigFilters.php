<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Imagine;

use Contributte\Imagist\Config\ConfigFilterCollection;
use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Filter\AbstractFilterMethodMapping;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use LogicException;

class ImagineConfigFilters extends AbstractFilterMethodMapping
{

	public function __construct(
		private ConfigFilterCollection $collection,
	)
	{
	}

	/**
	 * @phpstan-return array<string, callable(ImageInterface $source, ContextImageAware $context): void>
	 * @return array<string, callable> filter => method
	 */
	protected function getMapping(): array
	{
		$mapping = [];
		foreach ($this->collection->getFilters() as $filter) {
			$mapping[$filter->getName()] = [$this, '_invoke'];
		}

		return $mapping;
	}

	protected function supportsSource(object $source): bool
	{
		return $source instanceof ImageInterface;
	}

	public function _invoke(ImageInterface $image, ContextImageAware $context): void
	{
		$filter = $context->getImage()->getFilter();
		if (!$filter) {
			return; // unexpected behavior
		}

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
				$callback($image, ...$operation->getArguments());
			}
		}
	}

	protected function resize(ImageInterface $image, mixed ...$arguments): void
	{
		$image->resize(new Box(...$arguments));
	}

}
