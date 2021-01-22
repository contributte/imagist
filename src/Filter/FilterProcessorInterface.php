<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\File\FileInterface;

interface FilterProcessorInterface
{

	/**
	 * @param mixed[] $options
	 */
	public function process(FileInterface $target, FileInterface $source, array $options = []): string;

}
