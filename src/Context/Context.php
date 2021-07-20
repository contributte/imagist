<?php declare(strict_types = 1);

namespace Contributte\Imagist\Context;

final class Context implements ContextInterface
{

	/** @var mixed[] */
	private array $context;

	/**
	 * @param mixed[] $context
	 */
	public function __construct(array $context = [])
	{
		$this->context = $context;
	}

	public function has(string $key): bool
	{
		return isset($this->context[$key]);
	}

	public function get(string $key, mixed $default = null): mixed // phpcs:ignore SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilitySymbolRequired
	{
		return $this->context[$key] ?? $default;
	}

	public function set(string $key, mixed $value): ContextInterface
	{
		$this->context[$key] = $value;

		return $this;
	}

}
