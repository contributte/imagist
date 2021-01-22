<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\FileNameResolvers;

use Contributte\Imagist\File\FileFactoryInterface;
use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Resolver\FileNameResolverInterface;
use Nette\Utils\Random;

final class PrefixFileNameResolver implements FileNameResolverInterface
{

	private FileFactoryInterface $fileFactory;

	public function __construct(FileFactoryInterface $fileFactory)
	{
		$this->fileFactory = $fileFactory;
	}

	public function resolve(FileInterface $file): string
	{
		$image = $file->getImage();
		$name = $image->getName();
		$final = $name;
		while ($file->exists()) {
			$image = $image->withName($final = Random::generate() . '__' . $name);
			$file = $this->fileFactory->create($image);
		}

		return $final;
	}

}
