<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter;

use Contributte\Imagist\Config\ConfigFilterCollection;
use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\Filter\AbstractFilterMethodMapping;
use InvalidArgumentException;
use LogicException;
use Nette\Utils\Image;

class NetteConfigFilters extends AbstractFilterMethodMapping
{

	private ConfigFilterCollection $collection;

	public function __construct(ConfigFilterCollection $collection)
	{
		$this->collection = $collection;
	}
	/**
	 * @phpstan-return array<string, callable(Image $source, ContextImageAware $context): void>
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
		return $source instanceof Image;
	}

	public function _invoke(Image $image, ContextImageAware $context): void
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

	/**
	 * @param mixed ...$arguments
	 */
	protected function crop(Image $image, ...$arguments): void
	{
		$image->crop(...$arguments);
	}

	protected function flip(Image $image, string $mode): void
	{
		switch ($mode) {
			case 'horizontal':
				$mode = IMG_FLIP_HORIZONTAL;
				break;
			case 'vertical':
				$mode = IMG_FLIP_VERTICAL;
				break;
			case 'both':
				$mode = IMG_FLIP_BOTH;
				break;
			default:
				throw new InvalidArgumentException(
					'Operation flip value must be one of horizontal, vertical or both'
				);
		}

		$image->flip($mode);
	}

	/**
	 * @param mixed ...$arguments
	 */
	protected function resize(Image $image, ...$arguments): void
	{
		[$width, $height, $flag] = array_replace([
			1 => null,
			2 => 'fit',
		], $arguments);

		switch ($flag) {
			case 'fit':
				$flag = Image::FIT;
				break;
			case 'fill':
				$flag = Image::FILL;
				break;
			case 'exact':
				$flag = Image::EXACT;
				break;
			case 'shrink_only':
				$flag = Image::SHRINK_ONLY;
				break;
			case 'stretch':
				$flag = Image::STRETCH;
				break;
			default:
				throw new LogicException(sprintf('Resize flag %s not exists.', $flag));
		}

		$image->resize($width, $height, $flag);
	}

	protected function sharpen(Image $image): void
	{
		$image->sharpen();
	}

}
