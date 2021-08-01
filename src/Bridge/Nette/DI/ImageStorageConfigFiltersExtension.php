<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\DI;

use Contributte\Imagist\Config\ConfigFilter;
use Contributte\Imagist\Config\ConfigFilterCollection;
use Contributte\Imagist\Config\ConfigFilterOperation;
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
		return Expect::arrayOf(Expect::anyOf(Expect::type(Statement::class), Expect::listOf(Statement::class)));
	}

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('configFilterCollection'))
			->setFactory(ConfigFilterCollection::class);
	}

	public function beforeCompile()
	{
		/** @var mixed[] $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		$this->processConfigFilters($builder, $config);
	}

	/**
	 * @param mixed[] $config
	 */
	private function processConfigFilters(ContainerBuilder $builder, array $config): void
	{
		$service = $this->assertServiceDefinition(
			$builder->getDefinition($this->prefix('configFilterCollection'))
		);

		foreach ($config as $name => $filters) {
			if (!is_array($filters)) {
				$filters = [$filters];
			}

			$operations = [];
			foreach ($filters as $filter) {
				$operations[] = new Statement(ConfigFilterOperation::class, [$filter->getEntity(), $filter->arguments]);
			}

			$arguments = [new Statement(ConfigFilter::class, [$name, $operations])];

			$service->addSetup('addFilter', $arguments);
		}
	}

	private function assertServiceDefinition(Definition $definition): ServiceDefinition
	{
		assert($definition instanceof ServiceDefinition);

		return $definition;
	}

}
