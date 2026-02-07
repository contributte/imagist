<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$image = new PersistentImage('name/test.jpg');

	Assert::same('test.jpg', $image->getName());
	Assert::same('name', $image->getScope()->toString());
});
