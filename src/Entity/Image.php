<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

use Contributte\Imagist\Exceptions\ClosedImageException;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;

abstract class Image implements ImageInterface
{

	protected string $name;

	protected ?FilterInterface $filter = null;

	protected Scope $scope;

	private bool $closed = false;

	/** @var mixed[] */
	private array $closeBacktrace = [];

	private ?string $closedReason = null;

	public function __construct(string $name, ?Scope $scope = null)
	{
		$this->name = ltrim($name, '/');
		$this->scope = $scope ?? new Scope();
	}

	public function getId(): string
	{
		$this->throwIfClosed();

		return $this->scope->toStringWithTrailingSlash() . $this->name;
	}

	public function getName(): string
	{
		$this->throwIfClosed();

		return $this->name;
	}

	public function getSuffix(): ?string
	{
		$this->throwIfClosed();

		if (($pos = strrpos($this->name, '.')) === false) {
			return null;
		}

		return substr($this->name, $pos + 1);
	}

	public function getScope(): Scope
	{
		$this->throwIfClosed();

		return $this->scope;
	}

	public function getFilter(): ?FilterInterface
	{
		$this->throwIfClosed();

		return $this->filter;
	}

	public function hasFilter(): bool
	{
		$this->throwIfClosed();

		return (bool) $this->filter;
	}

	public function equalTo(ImageInterface $image): bool
	{
		if ($this->isEmpty() || $image->isEmpty()) {
			return false;
		}

		return $this->getId() === $image->getId();
	}

	/**
	 * @return static
	 */
	public function withScope(Scope $scope): static
	{
		$this->throwIfClosed();

		$clone = clone $this;
		$clone->scope = $scope;

		return $clone;
	}

	/**
	 * @return static
	 */
	public function withName(string $name): static
	{
		$this->throwIfClosed();

		$clone = clone $this;
		$clone->name = $name;

		return $clone;
	}

	/**
	 * @return static
	 */
	public function withFilter(?FilterInterface $filter): static
	{
		$this->throwIfClosed();

		$clone = clone $this;
		$clone->filter = $filter;

		return $clone;
	}

	/**
	 * @return static
	 */
	public function getOriginal(): static
	{
		$clone = clone $this;
		$clone->filter = null;

		return $clone;
	}

	public function isEmpty(): bool
	{
		return $this instanceof EmptyImageInterface;
	}

	public function isPromise(): bool
	{
		return false;
	}

	public function isClosed(): bool
	{
		return $this->closed;
	}

	protected function setClosed(?string $reason = null): void
	{
		$this->closedReason = $reason;
		$this->closed = true;
		$this->closeBacktrace = debug_backtrace();
	}

	protected function throwIfClosed(): void
	{
		if ($this->closed) {
			throw new ClosedImageException(
				sprintf(
					'Image %s is closed, reason: %s',
					$this->scope->toStringWithTrailingSlash() . $this->name,
					$this->closedReason ?: 'not specified'
				),
				$this->closeBacktrace
			);
		}
	}

	final public function __clone()
	{
		$this->throwIfClosed();
	}

}
