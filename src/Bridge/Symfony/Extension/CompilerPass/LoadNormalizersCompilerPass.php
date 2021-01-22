<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Symfony\Extension\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class LoadNormalizersCompilerPass implements CompilerPassInterface
{

	public const NORMALIZER_TAG = 'contributte.imagist.normalizer';

	public function process(ContainerBuilder $container): void
	{
		$service = $container->getDefinition('contributte.imagist.filter.normalizerCollection');

		foreach ($container->findTaggedServiceIds(self::NORMALIZER_TAG) as $serviceId => $tags) {
			$service->addMethodCall('add', [new Reference($serviceId)]);
		}
	}

}
