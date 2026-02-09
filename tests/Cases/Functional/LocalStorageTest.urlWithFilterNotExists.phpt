<?php declare(strict_types = 1);

namespace Tests\Cases\Functional;

use Contributte\Imagist\Builder\LocalImageStorageBuilder;
use Contributte\Imagist\Entity\PersistentImage;
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

	$linkGenerator = $result->getLinkGenerator();

	$persistent = new PersistentImage('image.jpg');
	$link = $linkGenerator->link($persistent->withFilter(new ThumbnailFilter()));

	Assert::null($link);

	$case->cleanup();
});
