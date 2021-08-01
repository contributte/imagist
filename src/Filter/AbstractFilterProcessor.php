<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Context\Context;
use Contributte\Imagist\Context\ContextImageAware;
use Contributte\Imagist\File\FileInterface;

abstract class AbstractFilterProcessor implements FilterProcessorInterface
{

	/** @var FilterInterface[] */
	private array $operations;

	/**
	 * @param FilterInterface[] $operations
	 */
	public function __construct(array $operations)
	{
		$this->operations = $operations;
	}
	/**
	 * @return $this
	 */
	public function addOperation(FilterInterface $operation)
	{
		$this->operations[] = $operation;

		return $this;
	}

	public function process(FileInterface $target, FileInterface $source, Context $context): string
	{
		$filter = $target->getImage()->getFilter();
		if (!$filter) {
			return $source->getContent();
		}

		$image = $this->createImageInstance($source);
		$context = new ContextImageAware($target->getImage(), $context);
		foreach ($this->operations as $operation) {
			if ($operation->supports($image, $filter, $context)) {
				$operation->operate($image, $filter, $context);
				break;
			}
		}

		return $this->imageInstanceToString($image, $target, $context);
	}

	abstract protected function createImageInstance(FileInterface $source): object;

	abstract protected function imageInstanceToString(object $image, FileInterface $source, ContextImageAware $context): string;

}
