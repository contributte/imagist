<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\FilterResolvers;

use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Resolver\FilterResolverInterface;
use LogicException;
use Nette\Utils\Json;

final class MD5FilterResolver implements FilterResolverInterface
{

	private const MAX_LENGTH = 255;

	public function resolve(FilterInterface $filter): string
	{
		$name = '_' . $filter->getName() . $this->optionsToString($filter->getOptions());
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
	 */
	private function optionsToString(array $options): string
	{
		return $options ? '-' . md5(Json::encode($options)) : '';
	}

}
