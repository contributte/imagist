<?php declare(strict_types = 1);

namespace Tests\Cases\Functional;

use Contributte\Imagist\Builder\LocalImageStorageBuilder;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\Uploader\FilePathUploader;
use Contributte\Tester\Toolkit;
use Tests\Fixtures\FileTestCase;

require_once __DIR__ . '/../../bootstrap.php';

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
