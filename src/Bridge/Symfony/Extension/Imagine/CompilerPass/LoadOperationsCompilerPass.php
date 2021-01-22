<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Symfony\Extension\Imagine\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class LoadOperationsCompilerPass implements CompilerPassInterface
{

	public const OPERATION_TAG = 'contributte.imagist.imagine.operation';

	public function process(ContainerBuilder $container): void
	{
		$loader = $container->getDefinition('contributte.imagist.imagine.operationRegistry');

		foreach ($container->findTaggedServiceIds(self::OPERATION_TAG) as $serviceId => $tags) {
			$loader->addMethodCall('add', [new Reference($serviceId)]);
		}
	}

}
