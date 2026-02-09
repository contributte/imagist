<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Scope\Scope;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$scope = new Scope('foo', 'middle', 'bar');

	Assert::true($scope->contains('foo'));
	Assert::true($scope->contains('middle'));
	Assert::true($scope->contains('bar'));
	Assert::false($scope->contains('not'));
});

Toolkit::test(static function (): void {
	$scope = new Scope();

	Assert::false($scope->contains('bar'));
});
