<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Functional;

use Contributte\Imagist\Bridge\Nette\Filter\NetteOperationProcessor;
use Contributte\Imagist\Bridge\Nette\Filter\NetteResourceFactory;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\Filter\FilterProcessor;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Persister\EmptyImagePersister;
use Contributte\Imagist\Persister\PersistentImagePersister;
use Contributte\Imagist\Persister\PersisterRegistry;
use Contributte\Imagist\Persister\StorableImagePersister;
use Contributte\Imagist\Remover\EmptyImageRemover;
use Contributte\Imagist\Remover\PersistentImageRemover;
use Contributte\Imagist\Remover\RemoverRegistry;
use Contributte\Imagist\Resolver\FileNameResolvers\OriginalFileNameResolver;
use Contributte\Imagist\Storage\ImageStorage;
use Contributte\Imagist\Testing\FileTestCase;
use Contributte\Imagist\Transaction\TransactionFactory;
use Contributte\Imagist\Transaction\TransactionFactoryInterface;
use Contributte\Imagist\Uploader\FilePathUploader;

class TransactionTest extends FileTestCase
{

	private TransactionFactoryInterface $transactionFactory;

	private ImageStorage $storage;

	protected function _before(): void
	{
		parent::_before();

		$processor = new FilterProcessor(
			new NetteResourceFactory(),
			[new NetteOperationProcessor()]
		);
		$fileFactory = new FileFactory(
			$filesystem = new LocalFilesystem($this->getAbsolutePath()),
			$pathInfoFactory = new PathInfoFactory()
		);

		$persisterRegistry = new PersisterRegistry();
		$persisterRegistry->add(new EmptyImagePersister());
		$persisterRegistry->add(new PersistentImagePersister($fileFactory, $processor));
		$persisterRegistry->add(new StorableImagePersister($fileFactory, $processor, new OriginalFileNameResolver()));

		$removerRegistry = new RemoverRegistry();
		$removerRegistry->add(new EmptyImageRemover());
		$removerRegistry->add(new PersistentImageRemover($fileFactory, $pathInfoFactory, $filesystem));

		$this->storage = $storage = new ImageStorage($persisterRegistry, $removerRegistry);

		$this->transactionFactory = new TransactionFactory($storage, $fileFactory);
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
