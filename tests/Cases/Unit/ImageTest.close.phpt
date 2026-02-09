<?php declare(strict_types = 1);

namespace Tests\Cases\Unit;

use Contributte\Imagist\Entity\Image;
use Contributte\Imagist\Exceptions\ClosedImageException;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

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
