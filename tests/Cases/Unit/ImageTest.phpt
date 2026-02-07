<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Entity\Image;
use Contributte\Imagist\Exceptions\ClosedImageException;
use Contributte\Imagist\Scope\Scope;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$image = new class ('foo.jpg', $scope = new Scope('bar')) extends Image {

	};

	Assert::same('foo.jpg', $image->getName());
	Assert::same('bar/foo.jpg', $image->getId());
	Assert::same('jpg', $image->getSuffix());
	Assert::same($scope, $image->getScope());
	Assert::notSame($image, $image->withName('bar'));
});

Toolkit::test(static function (): void {
	$image = new class('foo.jpg') extends Image {

		public function close(): void
		{
			$this->setClosed();
		}

	};

	Assert::false($image->isClosed());
	$image->close();

	Assert::true($image->isClosed());

	Assert::exception(static function () use ($image): void {
		$image->getId();
	}, ClosedImageException::class);
});
