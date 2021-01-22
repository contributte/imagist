<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Unit;

use Codeception\Test\Unit;
use Contributte\Imagist\Exceptions\InvalidArgumentException;
use Contributte\Imagist\Scope\Scope;

class ScopeTest extends Unit
{

	public function testOneScope(): void
	{
		$scope = new Scope('foo');

		$this->assertSame(['foo'], $scope->getScopes());
		$this->assertSame('foo', (string) $scope);
	}

	public function testStartsWith(): void
	{
		$scope = new Scope('foo', 'middle', 'bar');

		$this->assertTrue($scope->startsWith('foo'));
		$this->assertFalse($scope->startsWith('bar'));
	}

	public function testStartsWithEmptyArray(): void
	{
		$scope = new Scope();

		$this->assertFalse($scope->startsWith('foo'));
	}

	public function testEndsWith(): void
	{
		$scope = new Scope('foo', 'middle', 'bar');

		$this->assertTrue($scope->endsWith('bar'));
		$this->assertFalse($scope->endsWith('foo'));
	}

	public function testEndsWithEmptyArray(): void
	{
		$scope = new Scope();

		$this->assertFalse($scope->endsWith('bar'));
	}

	public function testContains(): void
	{
		$scope = new Scope('foo', 'middle', 'bar');

		$this->assertTrue($scope->contains('foo'));
		$this->assertTrue($scope->contains('middle'));
		$this->assertTrue($scope->contains('bar'));
		$this->assertFalse($scope->contains('not'));
	}

	public function testContainsWithEmptyArray(): void
	{
		$scope = new Scope();

		$this->assertFalse($scope->contains('bar'));
	}

	public function testMultiScope(): void
	{
		$scope = new Scope('foo', 'bar');

		$this->assertSame(['foo', 'bar'], $scope->getScopes());
		$this->assertSame('foo/bar', (string) $scope);
	}

	public function testWithAppendedScope(): void
	{
		$scope = new Scope('foo');
		$scope = $scope->withAppendedScopes('bar');

		$this->assertSame(['foo', 'bar'], $scope->getScopes());
		$this->assertSame('foo/bar', (string) $scope);
	}

	public function testWithPrependedScope(): void
	{
		$scope = new Scope('foo');
		$scope = $scope->withPrependedScopes('bar');

		$this->assertSame(['bar', 'foo'], $scope->getScopes());
		$this->assertSame('bar/foo', (string) $scope);
	}

	public function testFromString(): void
	{
		$scope = Scope::fromString('bar/foo');

		$this->assertSame(['bar', 'foo'], $scope->getScopes());
		$this->assertSame('bar/foo', (string) $scope);
	}

	public function testEmpty(): void
	{
		$this->expectException(InvalidArgumentException::class);

		new Scope('');
	}

	public function testInvalidChars(): void
	{
		$this->expectException(InvalidArgumentException::class);

		new Scope('bař');
	}

	public function testTrailingSlash(): void
	{
		$this->assertSame('foo/', (new Scope('foo'))->toStringWithTrailingSlash());
		$this->assertSame('', (new Scope())->toStringWithTrailingSlash());
	}

}
