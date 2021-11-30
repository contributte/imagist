<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Unit;

use Codeception\Test\Unit;
use Contributte\Imagist\Entity\Image;
use Contributte\Imagist\Exceptions\ClosedImageException;
use Contributte\Imagist\Scope\Scope;

class ImageTest extends Unit
{

	public function testImage(): void
	{
		$image = new class ('foo.jpg', $scope = new Scope('bar')) extends Image {

		};

		$this->assertSame('foo.jpg', $image->getName());
		$this->assertSame('bar/foo.jpg', $image->getId());
		$this->assertSame('jpg', $image->getSuffix());
		$this->assertSame($scope, $image->getScope());
		$this->assertNotSame($image, $new = $image->withName('bar'));
	}

	public function testClose(): void
	{
		$image = new class('foo.jpg') extends Image {

			public function close(): void
			{
				$this->setClosed();
			}

		};

		$this->assertFalse($image->isClosed());
		$image->close();

		$this->assertTrue($image->isClosed());

		$this->expectException(ClosedImageException::class);
		$image->getId();
	}

}
