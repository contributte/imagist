<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Symfony;

use Contributte\Imagist\Bridge\Imagine\OperationInterface;
use Contributte\Imagist\Bridge\Symfony\Extension\CompilerPass\LoadNormalizersCompilerPass;
use Contributte\Imagist\Bridge\Symfony\Extension\ImageStorageExtension;
use Contributte\Imagist\Bridge\Symfony\Extension\Imagine\CompilerPass\LoadOperationsCompilerPass;
use Contributte\Imagist\Filter\FilterNormalizerProcessorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ImageStorageBundle extends Bundle
{

	public function build(ContainerBuilder $container): void
	{
		parent::build($container);

		if (interface_exists(OperationInterface::class)) {
			$container->registerForAutoconfiguration(OperationInterface::class)
				->addTag(LoadOperationsCompilerPass::OPERATION_TAG);

			$container->addCompilerPass(new LoadOperationsCompilerPass());
		}

		$container->registerForAutoconfiguration(FilterNormalizerProcessorInterface::class)
			->addTag(LoadNormalizersCompilerPass::NORMALIZER_TAG);

		$container->addCompilerPass(new LoadNormalizersCompilerPass());
	}

	protected function getContainerExtensionClass(): string
	{
		return ImageStorageExtension::class;
	}

}
