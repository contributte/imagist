<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\File\FileInterface;

final class OriginalFilterProcessor implements FilterProcessorInterface
{

	/**
	 * @inheritDoc
	 */
	public function process(FileInterface $target, FileInterface $source, array $options = []): string
	{
		return $source->getContent();
	}

}
