<?php declare(strict_types = 1);

namespace Contributte\Imagist\Builder;

use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\Transaction\TransactionFactoryInterface;

final class ImageStorageBuilderResult
{

	private ImageStorageInterface $imageStorage;

	private LinkGeneratorInterface $linkGenerator;

	private TransactionFactoryInterface $transactionFactory;

	public function __construct(
		ImageStorageInterface $imageStorage,
		LinkGeneratorInterface $linkGenerator,
		TransactionFactoryInterface $transactionFactory
	)
	{
		$this->imageStorage = $imageStorage;
		$this->linkGenerator = $linkGenerator;
		$this->transactionFactory = $transactionFactory;
	}

	public function getImageStorage(): ImageStorageInterface
	{
		return $this->imageStorage;
	}

	public function getLinkGenerator(): LinkGeneratorInterface
	{
		return $this->linkGenerator;
	}

	public function getTransactionFactory(): TransactionFactoryInterface
	{
		return $this->transactionFactory;
	}

}
