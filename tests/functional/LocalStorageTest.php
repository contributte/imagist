<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Functional;

use Contributte\Imagist\Entity\EmptyImage;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\Entity\StorableImageInterface;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\LinkGenerator\LinkGenerator;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\Persister\EmptyImagePersister;
use Contributte\Imagist\Persister\PersistentImagePersister;
use Contributte\Imagist\Persister\PersisterRegistry;
use Contributte\Imagist\Persister\StorableImagePersister;
use Contributte\Imagist\Remover\EmptyImageRemover;
use Contributte\Imagist\Remover\PersistentImageRemover;
use Contributte\Imagist\Remover\RemoverRegistry;
use Contributte\Imagist\Resolver\DefaultImageResolvers\NullDefaultImageResolver;
use Contributte\Imagist\Resolver\DefaultImageResolvers\ScopeDefaultImageResolver;
use Contributte\Imagist\Resolver\FileNameResolvers\OriginalFileNameResolver;
use Contributte\Imagist\Scope\Scope;
use Contributte\Imagist\Storage\ImageStorage;
use Contributte\Imagist\Testing\FileTestCase;
use Contributte\Imagist\Testing\Filter\FilterProcessor;
use Contributte\Imagist\Testing\Filter\OperationRegistry;
use Contributte\Imagist\Testing\Filter\ThumbnailOperation;
use Contributte\Imagist\Uploader\FilePathUploader;

class LocalStorageTest extends FileTestCase
{

	private ImageStorage $storage;

	private LinkGenerator $linkGenerator;

	private FileFactory $fileFactory;

	protected function _before(): void
	{
		parent::_before();

		$registry = new OperationRegistry();
		$registry->add(new ThumbnailOperation());

		$processor = new FilterProcessor($registry);
		$this->fileFactory = new FileFactory(
			$filesystem = new LocalFilesystem($this->getAbsolutePath()),
			$pathInfoFactory = new PathInfoFactory()
		);
		$defaultImageResolver = new NullDefaultImageResolver();

		$persisterRegistry = new PersisterRegistry();
		$persisterRegistry->add(new EmptyImagePersister());
		$persisterRegistry->add(new PersistentImagePersister($this->fileFactory, $processor));
		$persisterRegistry->add(new StorableImagePersister($this->fileFactory, $processor, new OriginalFileNameResolver()));

		$removerRegistry = new RemoverRegistry();
		$removerRegistry->add(new EmptyImageRemover());
		$removerRegistry->add(new PersistentImageRemover($this->fileFactory, $pathInfoFactory, $filesystem));

		$this->storage = new ImageStorage($persisterRegistry, $removerRegistry);
		$this->linkGenerator = new LinkGenerator($this->storage, $this->fileFactory, $defaultImageResolver);
	}

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
		$image = $image->withFilter('thumbnail');

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

		$this->storage->persist($persistent->withFilter('thumbnail'));

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

		$this->storage->persist($persistent->withFilter('thumbnail'));

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

		$link = $this->linkGenerator->link($persistent->withFilter('thumbnail'));

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

		$link = $this->linkGenerator->link($persistent->withFilter('thumbnail'));

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

	private function createStorable(string $name = 'name.jpg'): StorableImageInterface
	{
		return new StorableImage(new FilePathUploader($this->imageJpg), $name);
	}

}
