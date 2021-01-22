<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Unit;

use Contributte\Imagist\Testing\FileTestCase;
use Contributte\Imagist\Uploader\StringUploader;

class StringUploaderTest extends FileTestCase
{

	public function testUpload(): void
	{
		$uploader = new StringUploader(file_get_contents($this->imageJpg));
		$this->assertSame(file_get_contents($this->imageJpg), $uploader->getContent());
	}

}
