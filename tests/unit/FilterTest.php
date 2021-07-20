<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Unit;

use Contributte\Imagist\Bridge\Imagine\FilterProcessor;
use Contributte\Imagist\Bridge\Imagine\OperationRegistry;
use Contributte\Imagist\Context\Context;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Testing\Bridge\Imagine\ThumbnailOperation;
use Contributte\Imagist\Testing\FileTestCase;
use Contributte\Imagist\Uploader\FilePathUploader;

class FilterTest extends FileTestCase
{

	private FilterProcessor $processor;

	protected function _before(): void
	{
		parent::_before();

		$registry = new OperationRegistry();
		$registry->add(new ThumbnailOperation());
		$this->processor = new FilterProcessor($registry);
	}

	public function testFilter(): void
	{
		$image = new StorableImage(
			new FilePathUploader($this->imageJpg),
			'name.jpg'
		);
		$image = $image->withFilter('thumbnail');

		$fileFactory = new FileFactory(new LocalFilesystem($this->getAbsolutePath()), new PathInfoFactory());

		$content = $this->processor->process(
			$fileFactory->create($image),
			$fileFactory->create($image->getOriginal()),
			new Context()
		);

		$size = getimagesizefromstring($content);
		$this->assertSame(15, $size[0], 'width is not same');
		$this->assertSame(15, $size[1], 'height is not same');
	}

}
