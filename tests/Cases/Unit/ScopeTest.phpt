<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Exceptions\InvalidArgumentException;
use Contributte\Imagist\Scope\Scope;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$scope = new Scope('foo');

	Assert::same(['foo'], $scope->getScopes());
	Assert::same('foo', (string) $scope);
});

Toolkit::test(static function (): void {
	$scope = new Scope('foo', 'middle', 'bar');

	Assert::true($scope->startsWith('foo'));
	Assert::false($scope->startsWith('bar'));
});

Toolkit::test(static function (): void {
	$scope = new Scope();

	Assert::false($scope->startsWith('foo'));
});

Toolkit::test(static function (): void {
	$scope = new Scope('foo', 'middle', 'bar');

	Assert::true($scope->endsWith('bar'));
	Assert::false($scope->endsWith('foo'));
});

Toolkit::test(static function (): void {
	$scope = new Scope();

	Assert::false($scope->endsWith('bar'));
});

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

Toolkit::test(static function (): void {
	$scope = new Scope('foo', 'bar');

	Assert::same(['foo', 'bar'], $scope->getScopes());
	Assert::same('foo/bar', (string) $scope);
});

Toolkit::test(static function (): void {
	$scope = new Scope('foo');
	$scope = $scope->withAppendedScopes('bar');

	Assert::same(['foo', 'bar'], $scope->getScopes());
	Assert::same('foo/bar', (string) $scope);
});

Toolkit::test(static function (): void {
	$scope = new Scope('foo');
	$scope = $scope->withPrependedScopes('bar');

	Assert::same(['bar', 'foo'], $scope->getScopes());
	Assert::same('bar/foo', (string) $scope);
});

Toolkit::test(static function (): void {
	$scope = Scope::fromString('bar/foo');

	Assert::same(['bar', 'foo'], $scope->getScopes());
	Assert::same('bar/foo', (string) $scope);
});

Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		new Scope('');
	}, InvalidArgumentException::class);
});

Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		new Scope('bař');
	}, InvalidArgumentException::class);
});

Toolkit::test(static function (): void {
	Assert::same('foo/', (new Scope('foo'))->toStringWithTrailingSlash());
	Assert::same('', (new Scope())->toStringWithTrailingSlash());
});
