<?php declare(strict_types = 1);

namespace Tests\Cases\Functional;

use Contributte\Imagist\Builder\LocalImageStorageBuilder;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\Uploader\FilePathUploader;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\FileTestCase;
use Tests\Fixtures\Filter\ThumbnailFilter;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();

	$image = new StorableImage(new FilePathUploader($case->imageJpg), 'name.jpg');
	$persistent = $storage->persist($image);

	$storage->persist($persistent->withFilter(new ThumbnailFilter()));

	$case->assertTempFileExists('media/name.jpg');
	$case->assertTempFileExists('cache/_thumbnail/name.jpg');
	$size = getimagesize($case->getAbsolutePath('cache/_thumbnail/name.jpg'));
	Assert::same(15, $size[0]);
	Assert::same(15, $size[1]);

	$case->cleanup();
});
