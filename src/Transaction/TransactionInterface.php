<?php declare(strict_types = 1);

namespace Contributte\Imagist\Transaction;

use Contributte\Imagist\Filter\Context\Context;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\PromisedImageInterface;
use Contributte\Imagist\Exceptions\RollbackFailedException;
use Contributte\Imagist\Exceptions\TransactionException;
use Contributte\Imagist\ImageStorageInterface;

interface TransactionInterface extends ImageStorageInterface
{

	public function isCommitted(): bool;

	public function commit(): void;

	/**
	 * @throws RollbackFailedException
	 * @throws TransactionException
	 */
	public function rollback(): void;

	/**
	 * @param mixed[] $context
	 */
	public function persist(ImageInterface $image, array $context = []): PromisedImageInterface;

	/**
	 * @param mixed[] $context
	 */
	public function remove(PersistentImageInterface $image, array $context = []): PromisedImageInterface;

}
