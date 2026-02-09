<?php declare(strict_types = 1);

namespace Tests\Cases\Functional;

use Contributte\Imagist\Builder\LocalImageStorageBuilder;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\Scope\Scope;
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

	$storage = $result->getImageStorage();

	$image = new StorableImage(new FilePathUploader($case->imageJpg), 'name.jpg', new Scope('namespace', 'scope'));
	$persistent = $storage->persist($image);

	$case->assertTempFileExists('media/namespace/scope/name.jpg');
	Assert::type(PersistentImageInterface::class, $persistent);
	Assert::same('namespace/scope/name.jpg', $persistent->getId());

	$case->cleanup();
});
