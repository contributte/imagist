<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\DI;

use Contributte\Imagist\Bridge\Gumlet\GumletLinkGenerator;
use Contributte\Imagist\Bridge\Gumlet\Normalizer\GumletConfigFiltersNormalizer;
use Contributte\Imagist\Bridge\Nette\DI\Config\GumletConfig;
use Contributte\Imagist\Filter\FilterNormalizerInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class GumletImageStorageExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::from(new GumletConfig());
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('normalizer'))
			->setFactory(GumletConfigFiltersNormalizer::class)
			->setType(FilterNormalizerInterface::class);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();
		assert($config instanceof GumletConfig);

		$definition = $builder->getDefinitionByType(LinkGeneratorInterface::class);
		assert($definition instanceof ServiceDefinition);

		$definition->setFactory(GumletLinkGenerator::class)
			->setArguments([
				'bucket' => $config->bucket,
				'token' => $config->token,
			]);

		$definition->addSetup('setDomain', [$config->domain]);
	}

}
