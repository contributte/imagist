<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;

interface FilterProcessorInterface
{

	public function process(FileInterface $target, FileInterface $source, ContextInterface $context): string;

}
