<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\StringFilter;

use Contributte\Imagist\Filter\FilterInterface;
use LogicException;

final class StringFilterCollection implements StringFilterCollectionInterface
{

	/** @var array<string, FilterInterface|DynamicFilterFactory<FilterInterface>> */
	private array $filters = [];

	/**
	 * @param FilterInterface|DynamicFilterFactory<FilterInterface> $filter
	 */
	public function add(FilterInterface|DynamicFilterFactory $filter, ?string $name = null): self
	{
		if ($name === null) {
			if (!$filter instanceof FilterInterface) {
				throw new LogicException(
					sprintf('Argument name must be passed or filter must be instance of %s.', FilterInterface::class)
				);
			}

			$name = $filter->getIdentifier()->getName();
		}

		$this->filters[$name] = $filter;

		return $this;
	}

	/**
	 * @param mixed[] $arguments
	 */
	public function get(string $name, array $arguments = []): FilterInterface
	{
		$filter = $this->filters[$name] ?? null;

		if (!$filter) {
			throw new LogicException(
				sprintf('String filter %s not exists.', $name)
			);
		}

		if ($filter instanceof DynamicFilterFactory) {
			$filter = $filter->create($arguments);
		} elseif ($arguments) {
			throw new LogicException(
				sprintf(
					'Cannot pass arguments to %s, passing arguments are allowed only for class of type %s.',
					$filter::class,
					DynamicFilterFactory::class
				)
			);
		}

		return new ParsedStringFilter($name, $arguments, $filter->getOperations());
	}

}
