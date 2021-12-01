<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Functional;

use Contributte\Imagist\Builder\LocalImageStorageBuilder;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\Testing\FileTestCase;
use Contributte\Imagist\Transaction\TransactionFactoryInterface;
use Contributte\Imagist\Uploader\FilePathUploader;

class TransactionTest extends FileTestCase
{

	private TransactionFactoryInterface $transactionFactory;

	private ImageStorageInterface $storage;

	protected function _before(): void
	{
		parent::_before();

		$builder = new LocalImageStorageBuilder($this->getAbsolutePath());
		$builder->withNetteFilterProcessor();
		$result = $builder->build();

		$this->storage = $result->getImageStorage();
		$this->transactionFactory = $result->getTransactionFactory();
	}

	public function testPreCommit(): void
	{
		$transaction = $this->transactionFactory->create();
		$transaction->persist(new StorableImage(new FilePathUploader($this->imageJpg), 'image.jpg'));

		$this->assertTempFileNotExists('media/image.jpg');
	}

	public function testCommit(): void
	{
		$transaction = $this->transactionFactory->create();
		$transaction->persist(new StorableImage(new FilePathUploader($this->imageJpg), 'image.jpg'));

		$transaction->commit();

		$this->assertTempFileExists('media/image.jpg');
	}

	public function testCommitRemove(): void
	{
		$transaction = $this->transactionFactory->create();
		$image = $this->storage->persist(new StorableImage(new FilePathUploader($this->imageJpg), 'image.jpg'));
		$this->assertTempFileExists('media/image.jpg');

		$transaction->remove($image);

		$this->assertTempFileExists('media/image.jpg');

		$transaction->commit();

		$this->assertTempFileNotExists('media/image.jpg');
	}

	public function testRollbackRemove(): void
	{
		$transaction = $this->transactionFactory->create();
		$image = $this->storage->persist(new StorableImage(new FilePathUploader($this->imageJpg), 'image.jpg'));
		$this->assertTempFileExists('media/image.jpg');

		$transaction->remove($image);

		$this->assertTempFileExists('media/image.jpg');

		$transaction->commit();

		$this->assertTempFileNotExists('media/image.jpg');

		$transaction->rollback();

		$this->assertTempFileExists('media/image.jpg');
		$this->assertFileEquals($this->imageJpg, $this->getAbsolutePath('media/image.jpg'));
	}

	public function testRollback(): void
	{
		$transaction = $this->transactionFactory->create();
		$transaction->persist(new StorableImage(new FilePathUploader($this->imageJpg), 'image.jpg'));

		$transaction->commit();

		$this->assertTempFileExists('media/image.jpg');

		$transaction->rollback();

		$this->assertTempFileNotExists('media/image.jpg');
	}

	public function testPersistAndRemoveSameImage(): void
	{
		$transaction = $this->transactionFactory->create();
		$image = $transaction->persist(new StorableImage(new FilePathUploader($this->imageJpg), 'image.jpg'));
		$transaction->remove($image);

		$transaction->commit();

		$this->assertTrue($image->isEmpty());

		$this->assertTempFileNotExists('media/image.jpg');
	}

	public function testRemoveAndPersistSameImage(): void
	{
		$transaction = $this->transactionFactory->create();
		$persistent = $this->storage->persist(new StorableImage(new FilePathUploader($this->imageJpg), 'image.jpg'));
		$image = $transaction->remove($persistent);
		$transaction->persist($image);

		$transaction->commit();

		$this->assertTempFileExists('media/image.jpg');
	}

}
