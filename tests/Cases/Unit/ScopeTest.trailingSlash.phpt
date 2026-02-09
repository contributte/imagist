<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Scope\Scope;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	Assert::same('foo/', (new Scope('foo'))->toStringWithTrailingSlash());
	Assert::same('', (new Scope())->toStringWithTrailingSlash());
});
