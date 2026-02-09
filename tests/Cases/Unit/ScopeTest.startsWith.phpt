<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Scope\Scope;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$scope = new Scope('foo', 'middle', 'bar');

	Assert::true($scope->startsWith('foo'));
	Assert::false($scope->startsWith('bar'));
});

Toolkit::test(static function (): void {
	$scope = new Scope();

	Assert::false($scope->startsWith('foo'));
});
