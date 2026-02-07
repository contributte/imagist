<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Uploader\StringUploader;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\FileTestCase;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$case = new FileTestCase();

	$uploader = new StringUploader(file_get_contents($case->imageJpg));
	Assert::same(file_get_contents($case->imageJpg), $uploader->getContent());

	$case->cleanup();
});
