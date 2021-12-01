<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\DI;

use Contributte\Imagist\Filter\StringFilter\CompositeStringFilter;
use Contributte\Imagist\Filter\StringFilter\DynamicFilterFactory;
use Contributte\Imagist\Filter\StringFilter\StringFilterCollection;
use Contributte\Imagist\Filter\StringFilter\StringFilterCollectionInterface;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

final class ImageStorageConfigFiltersExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'aliases' => Expect::arrayOf(Expect::string()),
			'filters' => Expect::arrayOf(Expect::anyOf(Expect::type(Statement::class), Expect::string())),
		]);
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
		/** @var stdClass $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		/** @var array<string, string> $aliases */
		$aliases = $config->aliases;
		$aliases['composite'] ??= CompositeStringFilter::class;
		$aliases['dynamic'] ??= DynamicFilterFactory::class;

		$filters = $this->processFilters($config->filters, $aliases);

		$service = $builder->getDefinition($this->prefix('configFilterCollection'));
		assert($service instanceof ServiceDefinition);

		foreach ($filters as $arguments) {
			$service->addSetup('add', $arguments);
		}
	}

	/**
	 * @param array<string|int, Statement|string> $statements
	 * @param array<string, string> $aliases
	 * @return array{Statement, string|null}[]
	 */
	private function processFilters(array $statements, array $aliases): array
	{
		$return = [];

		foreach ($statements as $name => $statement) {
			if (is_string($statement)) {
				$statement = new Statement($statement);
			} else {
				$statement = $this->processStatement($statement, $aliases);
			}

			if (!is_string($name)) {
				$name = null;
			} else if ($statement->getEntity() === DynamicFilterFactory::class && !isset($statement->arguments[1])) {
				$statement->arguments[1] = $name;
			}

			$return[] = [$statement, $name];
		}

		return $return;
	}

	/**
	 * @param Statement $statement
	 * @param array<string, string> $aliases
	 * @return Statement
	 */
	private function processStatement(Statement $statement, array $aliases): Statement
	{
		$entity = $statement->getEntity();
		$alias = null;

		if (is_string($entity) && isset($aliases[$entity])) {
			$alias = $aliases[$entity];
		}

		foreach ($statement->arguments as $key => $argument) {
			if ($argument instanceof Statement) {
				$statement->arguments[$key] = $this->processStatement($argument, $aliases);
			}
		}

		if ($alias) {
			return new Statement($alias, $statement->arguments);
		}

		return $statement;
 	}

}
