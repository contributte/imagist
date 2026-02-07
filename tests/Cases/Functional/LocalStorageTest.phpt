<?php declare(strict_types = 1);

namespace Tests\Cases\Functional;

use Contributte\Imagist\Builder\LocalImageStorageBuilder;
use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\LinkGenerator\LinkGenerator;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Resolver\DefaultImageResolvers\ScopeDefaultImageResolver;
use Contributte\Imagist\Scope\Scope;
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

	$case->assertTempFileExists('media/name.jpg');
	Assert::type(PersistentImageInterface::class, $persistent);
	Assert::same('name.jpg', $persistent->getId());

	$case->cleanup();
});

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

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();

	$image = new StorableImage(new FilePathUploader($case->imageJpg), 'name.jpg');
	$persistent = $storage->persist($image);

	$case->assertTempFileExists('media/name.jpg');

	$storage->remove($persistent);
	$case->assertTempFileNotExists('media/name.jpg');

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();

	$image = new StorableImage(new FilePathUploader($case->imageJpg), 'name.jpg', new Scope('namespace', 'scope'));
	$persistent = $storage->persist($image);

	$case->assertTempFileExists('media/namespace/scope/name.jpg');

	$storage->remove($persistent);
	$case->assertTempFileNotExists('media/name.jpg');

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();
	$linkGenerator = $result->getLinkGenerator();

	$image = new StorableImage(new FilePathUploader($case->imageJpg), 'name.jpg');
	$persistent = $storage->persist($image);

	Assert::same('/media/name.jpg', $linkGenerator->link($persistent));

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();

	$image = new StorableImage(new FilePathUploader($case->imageJpg), 'name.jpg');
	$image = $image->withFilter(new ThumbnailFilter());

	$storage->persist($image);

	$case->assertTempFileExists('media/name.jpg');
	$size = getimagesize($case->getAbsolutePath('media/name.jpg'));
	Assert::same(15, $size[0]);
	Assert::same(15, $size[1]);

	$case->cleanup();
});

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

	$storage->remove($persistent);

	$case->assertTempFileNotExists('media/name.jpg');
	$case->assertTempFileNotExists('cache/_thumbnail/name.jpg');

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();
	$linkGenerator = $result->getLinkGenerator();

	$image = new StorableImage(new FilePathUploader($case->imageJpg), 'name.jpg');
	$persistent = $storage->persist($image);

	$link = $linkGenerator->link($persistent->withFilter(new ThumbnailFilter()));

	Assert::same('/cache/_thumbnail/name.jpg', $link);
	$case->assertTempFileExists('media/name.jpg');
	$case->assertTempFileExists('cache/_thumbnail/name.jpg');
	$size = getimagesize($case->getAbsolutePath('cache/_thumbnail/name.jpg'));
	Assert::same(15, $size[0]);
	Assert::same(15, $size[1]);

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$linkGenerator = $result->getLinkGenerator();

	$persistent = new PersistentImage('image.jpg');
	$link = $linkGenerator->link($persistent->withFilter(new ThumbnailFilter()));

	Assert::null($link);

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$linkGenerator = $result->getLinkGenerator();

	$persistent = new PersistentImage('image.jpg');
	$link = $linkGenerator->link($persistent);

	Assert::null($link);

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();
	$fileFactory = new FileFactory(new LocalFilesystem($case->getAbsolutePath()), new PathInfoFactory());

	$linkGenerator = new LinkGenerator($storage, $fileFactory, new ScopeDefaultImageResolver([
		'foo' => 'noimage/foo.png',
	]));

	$storage->persist(
		(new StorableImage(new FilePathUploader($case->imageJpg), 'foo.png'))
			->withScope(new Scope('noimage'))
	);

	Assert::null($linkGenerator->link(new PersistentImage('bar.png')));
	Assert::null($linkGenerator->link(new EmptyImage()));
	Assert::same('/media/noimage/foo.png', $linkGenerator->link(new PersistentImage('foo/bar.png')));
	Assert::same('/media/noimage/foo.png', $linkGenerator->link(new EmptyImage(new Scope('foo'))));
	Assert::same('/media/noimage/foo.png', $linkGenerator->link(new EmptyImage(), ['scope' => 'foo']));

	$case->cleanup();
});

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$builder = new LocalImageStorageBuilder($case->getAbsolutePath());
	$builder->withNetteFilterProcessor();
	$result = $builder->build();

	$storage = $result->getImageStorage();
	$fileFactory = new FileFactory(new LocalFilesystem($case->getAbsolutePath()), new PathInfoFactory());

	$linkGenerator = new LinkGenerator($storage, $fileFactory, new ScopeDefaultImageResolver([
		'foo' => 'noimage/foo.png',
	]));

	Assert::null($linkGenerator->link(new PersistentImage('foo/bar.png')));

	$case->cleanup();
});
