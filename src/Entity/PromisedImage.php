<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Exceptions\PromiseException;
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

	public function getFilter(): ?ImageFilter
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
	public function withScope(Scope $scope): PersistentImageInterface
	{
		return $this->getResult()->withScope($scope);
	}

	/**
	 * @inheritDoc
	 */
	public function withName(string $name): PersistentImageInterface
	{
		return $this->getResult()->withName($name);
	}

	/**
	 * @inheritDoc
	 */
	public function withFilter(string $name, array $options = []): PersistentImageInterface
	{
		return $this->getResult()->withFilter($name, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function withFilterObject(?ImageFilter $filter): PersistentImageInterface
	{
		return $this->getResult()->withFilterObject($filter);
	}

	public function getOriginal(): PersistentImageInterface
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
		if (!$this->result) {
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

	public static function getSourceId(PromisedImage $image): string
	{
		return $image->source->getId();
	}

}
