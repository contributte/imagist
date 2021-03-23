<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Symfony\Extension;

use Contributte\Imagist\Bridge\Imagine\FilterProcessor;
use Contributte\Imagist\Bridge\Imagine\OperationInterface;
use Contributte\Imagist\Bridge\Imagine\OperationRegistryInterface;
use Contributte\Imagist\File\FileFactory;
use Contributte\Imagist\File\FileFactoryInterface;
use Contributte\Imagist\Filesystem\FilesystemInterface;
use Contributte\Imagist\Filesystem\LocalFilesystem;
use Contributte\Imagist\Filter\FilterNormalizerCollection;
use Contributte\Imagist\Filter\FilterNormalizerCollectionInterface;
use Contributte\Imagist\Filter\FilterProcessorInterface;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\LinkGenerator\LinkGenerator;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\PathInfo\PathInfoFactory;
use Contributte\Imagist\PathInfo\PathInfoFactoryInterface;
use Contributte\Imagist\Resolver\BucketResolverInterface;
use Contributte\Imagist\Resolver\BucketResolvers\BucketResolver;
use Contributte\Imagist\Resolver\FileNameResolverInterface;
use Contributte\Imagist\Resolver\FileNameResolvers\PrefixFileNameResolver;
use Contributte\Imagist\Resolver\FilterResolverInterface;
use Contributte\Imagist\Resolver\FilterResolvers\OriginalFilterResolver;
use Contributte\Imagist\Storage\ImageStorage;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class ImageStorageExtension extends Extension
{

	/**
	 * @param string[] $configs
	 */
	public function load(array $configs, ContainerBuilder $container): void
	{
		$this->loadFilesystem($container);
		$this->loadResolvers($container);
		$this->loadPathInfo($container);
		$this->loadFile($container);
		$this->loadFilter($container);

		if (interface_exists(OperationInterface::class)) {
			$this->loadImagineExtension($container);
		}

		$container->register('contributte.imagist.storage', ImageStorage::class)
			->setAutowired(true);

		$container->setAlias(ImageStorageInterface::class, 'contributte.imagist.storage');

		$container->register('contributte.imagist.linkGenerator', LinkGenerator::class)
			->setAutowired(true);

		$container->setAlias(LinkGeneratorInterface::class, 'contributte.imagist.linkGenerator');
	}

	private function loadResolvers(ContainerBuilder $container): void
	{
		$container->register('contributte.imagist.resolvers.bucket', BucketResolver::class)
			->setAutowired(true);

		$container->setAlias(BucketResolverInterface::class, 'contributte.imagist.resolvers.bucket');

		$container->register('contributte.imagist.resolvers.name', PrefixFileNameResolver::class)
			->setAutowired(true);

		$container->setAlias(FileNameResolverInterface::class, 'contributte.imagist.resolvers.name');

		$container->register('contributte.imagist.resolvers.filter', OriginalFilterResolver::class)
			->setAutowired(true);

		$container->setAlias(FilterResolverInterface::class, 'contributte.imagist.resolvers.filter');
	}

	private function loadImagineExtension(ContainerBuilder $container): void
	{
		$container->register('contributte.imagist.imagine.filterProcessor', FilterProcessor::class)
			->setAutowired(true);

		$container->setAlias(FilterProcessorInterface::class, 'contributte.imagist.imagine.filterProcessor');

		$container->register('contributte.imagist.imagine.operationRegistry', OperationRegistryInterface::class)
			->setAutowired(true);

		$container->setAlias(OperationRegistryInterface::class, 'contributte.imagist.imagine.operationRegistry');
	}

	private function loadFilesystem(ContainerBuilder $container): void
	{
		$container->register('contributte.imagist.filesystem', LocalFilesystem::class)
			->setArgument(0, '%kernel.project_dir%/public')
			->setAutowired(true);

		$container->setAlias(FilesystemInterface::class, 'contributte.imagist.filesystem');
	}

	private function loadPathInfo(ContainerBuilder $container): void
	{
		$container->register('contributte.imagist.pathInfoFactory', PathInfoFactory::class)
			->setAutowired(true);

		$container->setAlias(PathInfoFactoryInterface::class, 'contributte.imagist.pathInfoFactory');
	}

	private function loadFile(ContainerBuilder $container): void
	{
		$container->register('contributte.imagist.fileFactory', FileFactory::class)
			->setAutowired(true);

		$container->setAlias(FileFactoryInterface::class, 'contributte.imagist.fileFactory');
	}

	private function loadFilter(ContainerBuilder $container): void
	{
		$container->register('contributte.imagist.filter.normalizerCollection', FilterNormalizerCollection::class)
			->setAutowired(true);

		$container->setAlias(FilterNormalizerCollectionInterface::class, 'contributte.imagist.filter.normalizerCollection');
	}

}
