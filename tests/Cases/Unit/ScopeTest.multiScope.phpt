<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Scope\Scope;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$scope = new Scope('foo', 'bar');

	Assert::same(['foo', 'bar'], $scope->getScopes());
	Assert::same('foo/bar', (string) $scope);
});
