<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Exceptions\InvalidArgumentException;
use Contributte\Imagist\Scope\Scope;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		new Scope('');
	}, InvalidArgumentException::class);
});
