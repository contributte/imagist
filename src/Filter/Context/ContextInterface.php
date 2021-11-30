<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Context;

interface ContextInterface
{

	public function has(string $key): bool;

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function get(string $key, $default = null); // phpcs:ignore SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilitySymbolRequired
	/**
	 * @param mixed $value
	 */
	public function set(string $key, $value): ContextInterface;

}
