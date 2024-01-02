<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

use Contributte\Imagist\Exceptions\PromiseException;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;
use Contributte\Imagist\Transaction\TransactionInterface;

final class PromisedImage implements PromisedImageInterface
{

	private TransactionInterface $transaction;

	private ImageInterface $source;

	private ?PersistentImageInterface $result = null;

	/** @var callable[] */
	private array $then = [];

	private bool $remove;

	public function __construct(TransactionInterface $transaction, ImageInterface $source, bool $remove)
	{
		$this->transaction = $transaction;
		$this->source = $source;
		$this->remove = $remove;
	}

	public static function getSourceId(PromisedImage $image): string
	{
		return $image->source->getId();
	}

	public function getId(): string
	{
		return $this->getResult()->getId();
	}

	public function getName(): string
	{
		return $this->getResult()->getName();
	}

	public function getSuffix(): ?string
	{
		return $this->getResult()->getSuffix();
	}

	public function getScope(): Scope
	{
		return $this->getResult()->getScope();
	}

	public function getFilter(): ?FilterInterface
	{
		return $this->getResult()->getFilter();
	}

	public function hasFilter(): bool
	{
		return $this->getResult()->hasFilter();
	}

	/**
	 * @inheritDoc
	 */
	public function withScope(Scope $scope): static
	{
		return $this->getResult()->withScope($scope);
	}

	/**
	 * @inheritDoc
	 */
	public function withName(string $name): static
	{
		return $this->getResult()->withName($name);
	}

	/**
	 * @inheritDoc
	 */
	public function withFilter(?FilterInterface $filter): static
	{
		return $this->getResult()->withFilter($filter);
	}

	public function getOriginal(): static
	{
		return $this->getResult()->getOriginal();
	}

	public function isClosed(): bool
	{
		return $this->getResult()->isClosed();
	}

	public function isEmpty(): bool
	{
		if (!$this->isPending()) {
			return $this->getResult()->isEmpty();
		}

		return $this->remove;
	}

	public function isPromise(): bool
	{
		return true;
	}

	public function equalTo(ImageInterface $image): bool
	{
		if ($this->isEmpty() || $image->isEmpty()) {
			return false;
		}

		return $this->getId() === $image->getId();
	}

	public function process(callable $action): void
	{
		if ($this->result) {
			throw new PromiseException('Promised image is already processed');
		}

		$this->result = $action($this->source);

		foreach ($this->then as $callback) {
			$callback($this->result);
		}
	}

	public function then(callable $callable): void
	{
		if ($this->result) {
			throw new PromiseException('Promised image is already processed');
		}

		$this->then[] = $callable;
	}

	public function getResult(): PersistentImageInterface
	{
		if ($this->result === null) {
			throw new PromiseException('Promise is still pending');
		}

		return $this->result;
	}

	public function getTransaction(): TransactionInterface
	{
		return $this->transaction;
	}

	public function isPending(): bool
	{
		return !$this->result;
	}

	public function close(?string $reason = null): void
	{
		$this->getResult()->close($reason);
	}

}
