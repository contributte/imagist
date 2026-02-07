<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Bridge\Imagine\ImagineOperationProcessor;
use Contributte\Imagist\Bridge\Imagine\ImagineResourceFactory;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\Filter\Context\Context;
use Contributte\Imagist\Filter\FilterProcessor;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Uploader\FilePathUploader;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\FileTestCase;
use Tests\Fixtures\Filter\ImagineThumbnailFilter;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$processor = new FilterProcessor(
		new ImagineResourceFactory(),
		[new ImagineOperationProcessor()],
	);

	$image = new StorableImage(
		new FilePathUploader($case->imageJpg),
		'name.jpg'
	);
	$image = $image->withFilter(new ImagineThumbnailFilter());

	$fileFactory = new FileFactory(new LocalFilesystem($case->getAbsolutePath()), new PathInfoFactory());

	$content = $processor->process(
		$fileFactory->create($image),
		$fileFactory->create($image->getOriginal()),
		new Context()
	);

	$size = getimagesizefromstring($content);
	Assert::same(15, $size[0]);
	Assert::same(15, $size[1]);

	$case->cleanup();
});
