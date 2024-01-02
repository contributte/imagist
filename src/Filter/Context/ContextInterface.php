<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Context;

interface ContextInterface
{

	public function has(string $key): bool;

	public function get(string $key, mixed $default = null): mixed; // phpcs:ignore SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilitySymbolRequired
	public function set(string $key, mixed $value): ContextInterface;

}
