<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Unit;

use Contributte\Imagist\Bridge\Imagine\ImagineOperationProcessor;
use Contributte\Imagist\Bridge\Imagine\ImagineResourceFactory;
use Contributte\Imagist\Filter\Context\Context;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\Filter\FilterProcessor;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Testing\FileTestCase;
use Contributte\Imagist\Testing\Filter\ImagineThumbnailFilter;
use Contributte\Imagist\Uploader\FilePathUploader;

class FilterTest extends FileTestCase
{

	private FilterProcessor $processor;

	protected function _before(): void
	{
		parent::_before();

		$this->processor = new FilterProcessor(
			new ImagineResourceFactory(),
			[new ImagineOperationProcessor()],
		);
	}

	public function testFilter(): void
	{
		$image = new StorableImage(
			new FilePathUploader($this->imageJpg),
			'name.jpg'
		);
		$image = $image->withFilter(new ImagineThumbnailFilter());

		$fileFactory = new FileFactory(new LocalFilesystem($this->getAbsolutePath()), new PathInfoFactory());

		$content = $this->processor->process(
			$fileFactory->create($image),
			$fileFactory->create($image->getOriginal()),
			new Context()
		);

		$size = getimagesizefromstring($content);
		$this->assertSame(15, $size[0]);
		$this->assertSame(15, $size[1]);
	}

}
