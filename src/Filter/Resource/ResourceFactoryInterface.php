<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Resource;

use Contributte\Imagist\File\FileInterface;
use Contributte\Imagist\Filter\Context\ContextInterface;

interface ResourceFactoryInterface
{

	public function create(FileInterface $source, ContextInterface $context): object;

	public function toString(object $resource, FileInterface $source, ContextInterface $context): string;

}
