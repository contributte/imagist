<?php declare(strict_types = 1);

namespace Tests\Unit;

use Contributte\Imagist\Uploader\StringUploader;
use Tests\Testing\FileTestCase;

class StringUploaderTest extends FileTestCase
{

	public function testUpload(): void
	{
		$uploader = new StringUploader(file_get_contents($this->imageJpg));
		$this->assertSame(file_get_contents($this->imageJpg), $uploader->getContent());
	}

}
