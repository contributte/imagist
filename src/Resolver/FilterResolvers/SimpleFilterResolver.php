<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\FilterResolvers;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Resolver\FilterResolverInterface;
use LogicException;

final class SimpleFilterResolver implements FilterResolverInterface
{

	private const MAX_LENGTH = 255;

	public function resolve(FilterInterface $filter): string
	{
		$options = $this->parseArguments($filter->getIdentifier()->getArguments());
		$name = '_' . $filter->getIdentifier()->getName() . ($options ? '-' . implode('-', $options) : '');
		$length = strlen($name);

		if ($length > self::MAX_LENGTH) {
			throw new LogicException(
				sprintf('Maximum length of directory must be equal or less than 255, %d given.', $length)
			);
		}

		return $name;
	}

	/**
	 * @param mixed[] $options
	 * @return mixed[]
	 */
	private function parseArguments(array $options): array
	{
		$listKey = 0;

		// @phpcs:ignore
		foreach ($options as $key => &$value) {
			if ($key !== $listKey) {
				throw new LogicException(
					sprintf('%s only supports option list, given array.', self::class)
				);
			}

			if (is_bool($value)) {
				$value = (string) (int) $value;
			} elseif (is_string($value)) {
				if (!preg_match('#^[\w]+$#', $value)) {
					throw new LogicException(
						sprintf('%s only supports string contains a-z, A-Z and _', self::class)
					);
				}
			} elseif (is_int($value)) {
				$value = (string) $value;
			} else {
				throw new LogicException(
					sprintf('%s only supports int, string or bool values.', self::class)
				);
			}

			$listKey++;
		}

		return $options;
	}

}
