<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\DI;

use Contributte\Imagist\Filter\StringFilter\StringFilterCollection;
use Contributte\Imagist\Filter\StringFilter\StringFilterCollectionInterface;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Definition;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class ImageStorageConfigFiltersExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::arrayOf(Expect::type(Statement::class));
	}

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('configFilterCollection'))
			->setType(StringFilterCollectionInterface::class)
			->setFactory(StringFilterCollection::class);
	}

	public function beforeCompile()
	{
		/** @var Statement[] $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		$service = $builder->getDefinition($this->prefix('configFilterCollection'));
		assert($service instanceof ServiceDefinition);

		foreach ($config as $name => $statement) {
			$service->addSetup('add', [$name, $statement]);
		}
//		$this->processConfigFilters($builder, $config);
	}

}
