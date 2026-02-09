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

	$transactionFactory = $result->getTransactionFactory();

	$transaction = $transactionFactory->create();
	$transaction->persist(new StorableImage(new FilePathUploader($case->imageJpg), 'image.jpg'));

	$transaction->commit();

	$case->assertTempFileExists('media/image.jpg');

	$case->cleanup();
});
