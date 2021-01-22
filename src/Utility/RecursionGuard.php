<?php declare(strict_types = 1);

namespace Contributte\Imagist\Utility;

trait RecursionGuard
{

	/**
	 * @param mixed[] $options
	 */
	public function isRecursion(array $options): bool
	{
		return isset($options['_recursion'][static::class]);
	}

	/**
	 * @param mixed[] $options
	 * @return mixed[]
	 */
	public function setRecursion(array $options): array
	{
		$options['_recursion'][static::class] = true;

		return $options;
	}

}
