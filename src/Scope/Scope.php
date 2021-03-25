<?php declare(strict_types = 1);

namespace Contributte\Imagist\Scope;

use Contributte\Imagist\Exceptions\InvalidArgumentException;

class Scope
{

	/** @var string[] */
	private array $scopes = [];

	final public function __construct(string ...$scopes)
	{
		foreach ($scopes as $scope) {
			$this->addScope($scope);
		}
	}

	public function isEmpty(): bool
	{
		return !$this->scopes;
	}

	public function startsWith(string $scope): bool
	{
		if (!$this->scopes) {
			return false;
		}

		return $this->scopes[0] === $scope;
	}

	public function endsWith(string $scope): bool
	{
		if (!$this->scopes) {
			return false;
		}

		return $this->scopes[array_key_last($this->scopes)] === $scope;
	}

	public function contains(string $scope): bool
	{
		if (!$this->scopes) {
			return false;
		}

		return in_array($scope, $this->scopes);
	}

	public function equals(string... $scopes): bool
	{
		$index = 0;
		foreach ($scopes as $scope) {
			if (($this->scopes[$index] ?? null) !== $scope) {
				return false;
			}

			$index++;
		}

		return true;
	}

	protected function addScope(string $scope): void
	{
		$scope = trim($scope, " \t\n\r\0\v/");
		if (!$scope) {
			throw new InvalidArgumentException(sprintf('Scope must not be empty'));
		}

		if (!ctype_alnum(str_replace(['_', '-'], '', $scope))) {
			throw new InvalidArgumentException(sprintf('Scope "%s" contains invalid chars', $scope));
		}

		if ($scope[0] === '_') {
			throw new InvalidArgumentException(sprintf('Scope "%s" must not start with _', $scope));
		}

		$this->scopes[] = $scope;
	}

	/**
	 * @return static
	 */
	public function withAppendedScopes(string ...$scopes)
	{
		return new static(...$this->scopes, ...$scopes);
	}

	/**
	 * @return static
	 */
	public function withPrependedScopes(string ...$scopes)
	{
		return new static(...$scopes, ...$this->scopes);
	}

	/**
	 * @return string[]
	 */
	public function getScopes(): array
	{
		return $this->scopes;
	}

	/**
	 * @return static
	 */
	public static function fromString(string $scope)
	{
		return new static(...explode('/', $scope));
	}

	public function toStringWithTrailingSlash(): string
	{
		return $this->scopes ? (string) $this . '/' : '';
	}

	public function toString(): string
	{
		return implode('/', $this->scopes);
	}

	public function toNullableString(): ?string
	{
		if ($this->isEmpty()) {
			return null;
		}

		return implode('/', $this->scopes);
	}

	public function __toString(): string
	{
		return $this->toString();
	}

}
