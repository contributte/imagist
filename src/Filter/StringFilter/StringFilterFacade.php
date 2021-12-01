<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\StringFilter;

use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Filter\FilterInterface;
use LogicException;

final class StringFilterFacade
{

	public static function resolveByFilter(
		?StringFilterCollectionInterface $collection,
		FilterInterface $filter
	): FilterInterface
	{
		if ($filter instanceof StringFilter) {
			if (!$collection) {
				throw new LogicException(
					sprintf(
						'Class %s have to be set if you want use string filters.',
						StringFilterCollectionInterface::class
					)
				);
			}

			return $collection->get($filter->getName(), $filter->getArguments());
		}

		return $filter;
	}

	/**
	 * @template T of ImageInterface
	 * @param T $image
	 * @return T
	 */
	public static function resolveByImage(
		?StringFilterCollectionInterface $collection,
		ImageInterface $image
	): ImageInterface
	{
		if ($filter = $image->getFilter()) {
			return $image->withFilter(self::resolveByFilter($collection, $filter));
		}

		return $image;
	}

	/**
	 * @template T of ImageInterface
	 * @param T|null $image
	 * @return T|null
	 */
	public static function resolveByNullableImage(
		?StringFilterCollectionInterface $collection,
		?ImageInterface $image
	): ?ImageInterface
	{
		if (!$image) {
			return null;
		}

		if ($filter = $image->getFilter()) {
			return $image->withFilter(self::resolveByFilter($collection, $filter));
		}

		return $image;
	}

}
