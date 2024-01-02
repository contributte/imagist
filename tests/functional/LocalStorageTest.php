<?php declare(strict_types = 1);

namespace Tests\Functional;

use Contributte\Imagist\Builder\LocalImageStorageBuilder;
use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\Entity\StorableImageInterface;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\LinkGenerator\LinkGenerator;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Resolver\DefaultImageResolvers\ScopeDefaultImageResolver;
use Contributte\Imagist\Scope\Scope;
use Contributte\Imagist\Uploader\FilePathUploader;
use Tests\Testing\FileTestCase;
use Tests\Testing\Filter\ThumbnailFilter;

class LocalStorageTest extends FileTestCase
{

	private ImageStorageInterface $storage;

	private LinkGeneratorInterface $linkGenerator;

	private FileFactory $fileFactory;

	public function testPersist(): void
	{
		$image = new StorableImage(new FilePathUploader($this->imageJpg), 'name.jpg');

		$persistent = $this->storage->persist($image);

		$this->assertTempFileExists('media/name.jpg');
		$this->assertInstanceOf(PersistentImageInterface::class, $persistent);
		$this->assertSame('name.jpg', $persistent->getId());
	}

	public function testPersistScope(): void
	{
		$image = new StorableImage(new FilePathUploader($this->imageJpg), 'name.jpg', new Scope('namespace', 'scope'));

		$persistent = $this->storage->persist($image);

		$this->assertTempFileExists('media/namespace/scope/name.jpg');
		$this->assertInstanceOf(PersistentImageInterface::class, $persistent);
		$this->assertSame('namespace/scope/name.jpg', $persistent->getId());
	}

	public function testRemove(): void
	{
		$image = new StorableImage(new FilePathUploader($this->imageJpg), 'name.jpg');

		$persistent = $this->storage->persist($image);

		$this->assertTempFileExists('media/name.jpg');

		$this->storage->remove($persistent);
		$this->assertTempFileNotExists('media/name.jpg');
	}

	public function testRemoveScope(): void
	{
		$image = new StorableImage(new FilePathUploader($this->imageJpg), 'name.jpg', new Scope('namespace', 'scope'));

		$persistent = $this->storage->persist($image);

		$this->assertTempFileExists('media/namespace/scope/name.jpg');

		$this->storage->remove($persistent);
		$this->assertTempFileNotExists('media/name.jpg');
	}

	public function testUrl(): void
	{
		$image = new StorableImage(new FilePathUploader($this->imageJpg), 'name.jpg');

		$persistent = $this->storage->persist($image);

		$this->assertSame('/media/name.jpg', $this->linkGenerator->link($persistent));
	}

	public function testFiltersWithNewUpload(): void
	{
		$image = new StorableImage(new FilePathUploader($this->imageJpg), 'name.jpg');
		$image = $image->withFilter(new ThumbnailFilter());

		$this->storage->persist($image);

		$this->assertTempFileExists('media/name.jpg');
		$size = getimagesize($this->getAbsolutePath('media/name.jpg'));
		$this->assertSame(15, $size[0]);
		$this->assertSame(15, $size[1]);
	}

	public function testFilterExistingImage(): void
	{
		$image = new StorableImage(new FilePathUploader($this->imageJpg), 'name.jpg');
		$persistent = $this->storage->persist($image);

		$this->storage->persist($persistent->withFilter(new ThumbnailFilter()));

		$this->assertTempFileExists('media/name.jpg');
		$this->assertTempFileExists('cache/_thumbnail/name.jpg');
		$size = getimagesize($this->getAbsolutePath('cache/_thumbnail/name.jpg'));
		$this->assertSame(15, $size[0]);
		$this->assertSame(15, $size[1]);
	}

	public function testFilterImageAndDelete(): void
	{
		$image = new StorableImage(new FilePathUploader($this->imageJpg), 'name.jpg');
		$persistent = $this->storage->persist($image);

		$this->storage->persist($persistent->withFilter(new ThumbnailFilter()));

		$this->assertTempFileExists('media/name.jpg');
		$this->assertTempFileExists('cache/_thumbnail/name.jpg');

		$this->storage->remove($persistent);

		$this->assertTempFileNotExists('media/name.jpg');
		$this->assertTempFileNotExists('cache/_thumbnail/name.jpg');
	}

	public function testToUrlWithFilter(): void
	{
		$image = new StorableImage(new FilePathUploader($this->imageJpg), 'name.jpg');
		$persistent = $this->storage->persist($image);

		$link = $this->linkGenerator->link($persistent->withFilter(new ThumbnailFilter()));

		$this->assertSame('/cache/_thumbnail/name.jpg', $link);
		$this->assertTempFileExists('media/name.jpg');
		$this->assertTempFileExists('cache/_thumbnail/name.jpg');
		$size = getimagesize($this->getAbsolutePath('cache/_thumbnail/name.jpg'));
		$this->assertSame(15, $size[0]);
		$this->assertSame(15, $size[1]);
	}

	public function testToUrlWithFilterAndImageNotExists(): void
	{
		$persistent = new PersistentImage('image.jpg');

		$link = $this->linkGenerator->link($persistent->withFilter(new ThumbnailFilter()));

		$this->assertNull($link);
	}

	public function testToUrlAndImageNotExists(): void
	{
		$persistent = new PersistentImage('image.jpg');

		$link = $this->linkGenerator->link($persistent);

		$this->assertNull($link);
	}

	public function testScopeDefaultImageResolve(): void
	{
		$linkGenerator = new LinkGenerator($this->storage, $this->fileFactory, new ScopeDefaultImageResolver([
			'foo' => 'noimage/foo.png',
		]));

		$this->storage->persist(
			$this->createStorable('foo.png')
				->withScope(new Scope('noimage'))
		);

		$this->assertNull($linkGenerator->link(new PersistentImage('bar.png')));
		$this->assertNull($linkGenerator->link(new EmptyImage()));
		$this->assertSame('/media/noimage/foo.png', $linkGenerator->link(new PersistentImage('foo/bar.png')));
		$this->assertSame('/media/noimage/foo.png', $linkGenerator->link(new EmptyImage(new Scope('foo'))));
		$this->assertSame('/media/noimage/foo.png', $linkGenerator->link(new EmptyImage(), ['scope' => 'foo']));
	}

	public function testScopeDefaultImageResolverRecursion(): void
	{
		$linkGenerator = new LinkGenerator($this->storage, $this->fileFactory, new ScopeDefaultImageResolver([
			'foo' => 'noimage/foo.png',
		]));

		$this->assertNull($linkGenerator->link(new PersistentImage('foo/bar.png')));
	}

	protected function _before(): void
	{
		parent::_before();

		$builder = new LocalImageStorageBuilder($this->getAbsolutePath());
		$builder->withNetteFilterProcessor();
		$result = $builder->build();

		$this->fileFactory = new FileFactory(new LocalFilesystem($this->getAbsolutePath()), new PathInfoFactory());
		$this->storage = $result->getImageStorage();
		$this->linkGenerator = $result->getLinkGenerator();
	}

	private function createStorable(string $name = 'name.jpg'): StorableImageInterface
	{
		return new StorableImage(new FilePathUploader($this->imageJpg), $name);
	}

}
