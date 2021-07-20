<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\Context\Context;
use Contributte\Imagist\File\FileInterface;

interface FilterProcessorInterface
{

	public function process(FileInterface $target, FileInterface $source, Context $context): string;

}
