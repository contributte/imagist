<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

use Contributte\Imagist\Transaction\TransactionInterface;

interface PromisedImageInterface extends PersistentImageInterface
{

	public function process(callable $action): void;

	public function isPending(): bool;

	public function then(callable $callable): void;

	public function getResult(): PersistentImageInterface;

	public function getTransaction(): TransactionInterface;

}
