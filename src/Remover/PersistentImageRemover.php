<?php declare(strict_types = 1);

namespace Contributte\Imagist\Remover;

use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\IndelibleImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\File\FileFactoryInterface;
use Contributte\Imagist\Filesystem\FilesystemInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;
use Contributte\Imagist\Filter\Internal\VoidFilter;
use Contributte\Imagist\PathInfo\PathInfoFactoryInterface;
use DomainException;
use Typertion\Php\ArrayTypeAssert;
use Typertion\Php\TypeAssert;

final class PersistentImageRemover implements RemoverInterface
{

	private FileFactoryInterface $fileFactory;

	private PathInfoFactoryInterface $pathInfoFactory;

	private FilesystemInterface $filesystem;

	public function __construct(FileFactoryInterface $fileFactory, PathInfoFactoryInterface $pathInfoFactory, FilesystemInterface $filesystem)
	{
		$this->fileFactory = $fileFactory;
		$this->pathInfoFactory = $pathInfoFactory;
		$this->filesystem = $filesystem;
	}

	public function supports(PersistentImageInterface $image, ContextInterface $context): bool
	{
		return !$image instanceof EmptyImageInterface;
	}

	public function remove(PersistentImageInterface $image, ContextInterface $context): void
	{
		if ($image instanceof IndelibleImage) {
			throw new DomainException('Cannot delete indelible image.');
		}

		$this->removeOriginal($image);
		$this->removeFiltered($image);

		$image->close('image removed');
	}

	private function removeFiltered(PersistentImageInterface $image): void
	{
		$path = $this->pathInfoFactory->create($image->withFilter(new VoidFilter('void')));

		foreach ($this->filesystem->listContents($path->toString($path::BUCKET | $path::SCOPE)) as $path) {
			$path = TypeAssert::array($path);

			if (ArrayTypeAssert::string($path, 'type') !== 'dir') {
				continue;
			}

			$filename = isset($path['filename']) ? TypeAssert::string($path['filename']) : '';
 			if (!$filename || $filename[0] !== '_') {
				continue;
			}

			$this->fileFactory->create($image->withFilter(new VoidFilter(substr($filename, 1))))
				->delete();
		}
	}

	private function removeOriginal(PersistentImageInterface $image): void
	{
		$this->fileFactory->create($image->getOriginal())
			->delete();
	}

}
