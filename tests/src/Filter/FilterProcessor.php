<?php declare(strict_types = 1);

namespace Contributte\Imagist\Testing\Filter;

use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Filter\FilterProcessorInterface;
use LogicException;
use Nette\Utils\Image;

final class FilterProcessor implements FilterProcessorInterface
{

	private OperationRegistryInterface $operationRegistry;

	public function __construct(OperationRegistryInterface $operationRegistry)
	{
		$this->operationRegistry = $operationRegistry;
	}

	/**
	 * @param mixed[] $options
	 */
	public function process(FileInterface $target, FileInterface $source, array $options = []): string
	{
		$filter = $target->getImage()->getFilter();
		if (!$filter) {
			return $target->getContent();
		}

		$operation = $this->operationRegistry->get($filter, $target->getImage()->getScope());

		if (!$operation) {
			throw new LogicException(sprintf('Operation not found for %s', $target->getImage()->getId()));
		}

		$operation->operate($image = Image::fromString($source->getContent(), $format), $filter);

		return $image->toString($format);
	}

}
