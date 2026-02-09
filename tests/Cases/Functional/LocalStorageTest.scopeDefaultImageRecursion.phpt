<?php declare(strict_types = 1);

namespace Tests\Cases\Functional;

use Contributte\Imagist\Builder\LocalImageStorageBuilder;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\LinkGenerator\LinkGenerator;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Resolver\DefaultImageResolvers\ScopeDefaultImageResolver;
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
	$fileFactory = new FileFactory(new LocalFilesystem($case->getAbsolutePath()), new PathInfoFactory());

	$linkGenerator = new LinkGenerator($storage, $fileFactory, new ScopeDefaultImageResolver([
		'foo' => 'noimage/foo.png',
	]));

	Assert::null($linkGenerator->link(new PersistentImage('foo/bar.png')));

	$case->cleanup();
});
