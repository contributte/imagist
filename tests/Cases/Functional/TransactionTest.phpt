<?php declare(strict_types = 1);

namespace Tests\Cases\Functional;

use Contributte\Imagist\Builder\LocalImageStorageBuilder;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\Uploader\FilePathUploader;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\FileTestCase;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$transactionFactory = $result->getTransactionFactory();

	$transaction = $transactionFactory->create();
	$transaction->persist(new StorableImage(new FilePathUploader($case->imageJpg), 'image.jpg'));

	$case->assertTempFileNotExists('media/image.jpg');

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$transactionFactory = $result->getTransactionFactory();

	$transaction = $transactionFactory->create();
	$transaction->persist(new StorableImage(new FilePathUploader($case->imageJpg), 'image.jpg'));

	$transaction->commit();

	$case->assertTempFileExists('media/image.jpg');

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();
	$transactionFactory = $result->getTransactionFactory();

	$transaction = $transactionFactory->create();
	$image = $storage->persist(new StorableImage(new FilePathUploader($case->imageJpg), 'image.jpg'));
	$case->assertTempFileExists('media/image.jpg');

	$transaction->remove($image);

	$case->assertTempFileExists('media/image.jpg');

	$transaction->commit();

	$case->assertTempFileNotExists('media/image.jpg');

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();
	$transactionFactory = $result->getTransactionFactory();

	$transaction = $transactionFactory->create();
	$image = $storage->persist(new StorableImage(new FilePathUploader($case->imageJpg), 'image.jpg'));
	$case->assertTempFileExists('media/image.jpg');

	$transaction->remove($image);

	$case->assertTempFileExists('media/image.jpg');

	$transaction->commit();

	$case->assertTempFileNotExists('media/image.jpg');

	$transaction->rollback();

	$case->assertTempFileExists('media/image.jpg');
	Assert::same(file_get_contents($case->imageJpg), file_get_contents($case->getAbsolutePath('media/image.jpg')));

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$transactionFactory = $result->getTransactionFactory();

	$transaction = $transactionFactory->create();
	$transaction->persist(new StorableImage(new FilePathUploader($case->imageJpg), 'image.jpg'));

	$transaction->commit();

	$case->assertTempFileExists('media/image.jpg');

	$transaction->rollback();

	$case->assertTempFileNotExists('media/image.jpg');

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$transactionFactory = $result->getTransactionFactory();

	$transaction = $transactionFactory->create();
	$image = $transaction->persist(new StorableImage(new FilePathUploader($case->imageJpg), 'image.jpg'));
	$transaction->remove($image);

	$transaction->commit();

	Assert::true($image->isEmpty());

	$case->assertTempFileNotExists('media/image.jpg');

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();
	$transactionFactory = $result->getTransactionFactory();

	$transaction = $transactionFactory->create();
	$persistent = $storage->persist(new StorableImage(new FilePathUploader($case->imageJpg), 'image.jpg'));
	$image = $transaction->remove($persistent);
	$transaction->persist($image);

	$transaction->commit();

	$case->assertTempFileExists('media/image.jpg');

	$case->cleanup();
});
