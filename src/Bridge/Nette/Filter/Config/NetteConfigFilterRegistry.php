<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter\Config;

use Contributte\Imagist\Bridge\Nette\Filter\NetteImageOptions;
use Contributte\Imagist\Bridge\Nette\Filter\NetteOperationInterface;
use Contributte\Imagist\Config\ConfigFilterStack;
use Contributte\Imagist\Exceptions\UnexpectedErrorException;
use Contributte\Imagist\Filter\FilterInterface;
use Contributte\Imagist\Scope\Scope;
use InvalidArgumentException;
use Nette\Utils\Image;

final class NetteConfigFilterRegistry implements NetteOperationInterface
{

	/** @var NetteConfigOperationInterface[] */
	protected array $operations = [];

	/** @var ConfigFilterStack[] */
	protected array $configFilterStacks = [];

	public function addConfigFilterStack(ConfigFilterStack $configFilterStack): void
	{
		$this->configFilterStacks[$configFilterStack->getName()] = $configFilterStack;
	}

	public function addOperation(NetteConfigOperationInterface $operation): void
	{
		$this->operations[$operation->getName()] = $operation;
	}

	public function supports(FilterInterface $filter, Scope $scope): bool
	{
		return isset($this->configFilterStacks[$filter->getName()]);
	}

	public function operate(Image $image, FilterInterface $filter, NetteImageOptions $options): void
	{
		if (!isset($this->configFilterStacks[$filter->getName()])) {
			throw new UnexpectedErrorException();
		}

		foreach ($this->configFilterStacks[$filter->getName()]->getConfigFilters() as $configFilter) {
			if (!isset($this->operations[$configFilter->getName()])) {
				throw new InvalidArgumentException(sprintf('Config operation %s not exists.', $configFilter->getName()));
			}

			$operation = $this->operations[$configFilter->getName()];

			$operation->operate($image, $filter, $options, $configFilter->getArguments());
		}
	}

}
