<?php declare(strict_types = 1);

namespace Contributte\Imagist\Transaction;

interface TransactionFactoryInterface
{

	public function create(): TransactionInterface;

}
