<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Context;

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

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function get(string $key, $default = null) // phpcs:ignore SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilitySymbolRequired
	{
		return $this->context[$key] ?? $default;
	}

	/**
	 * @param mixed $value
	 */
	public function set(string $key, $value): ContextInterface
	{
		$this->context[$key] = $value;

		return $this;
	}

}
