<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\DI;

use Contributte\Imagist\Bridge\Gumlet\GumletLinkGenerator;
use Contributte\Imagist\Bridge\Gumlet\GumletOperationProcessor;
use Contributte\Imagist\Bridge\Nette\DI\Config\GumletConfig;
use Contributte\Imagist\Filter\Operation\OperationProcessorInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\InvalidConfigurationException;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * @final
 */
/*final*/ class GumletImagistExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::from(new GumletConfig());
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('normalizer'))
			->setFactory(GumletOperationProcessor::class)
			->setType(OperationProcessorInterface::class);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig();
		assert($config instanceof GumletConfig);

		if (!$config->bucket && !$config->customDomain) {
			throw new InvalidConfigurationException('bucket or customDomain must be set.');
		}

		$definition = $builder->getDefinitionByType(LinkGeneratorInterface::class);
		assert($definition instanceof ServiceDefinition);

		$definition->setFactory(GumletLinkGenerator::class)
			->setArguments([
				'bucket' => $config->bucket,
				'token' => $config->token,
				'customDomain' => $config->customDomain,
			]);

		$definition->addSetup('setDomain', [$config->domain]);
	}

}
