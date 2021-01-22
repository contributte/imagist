<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter;

use Contributte\Imagist\File\FileInterface;
use LogicException;

final class VoidFilterProcessor implements FilterProcessorInterface
{

	/**
	 * @inheritDoc
	 */
	public function process(FileInterface $target, FileInterface $source, array $options = []): string
	{
		if ($target->getImage()->getFilter()) {
			throw new LogicException(sprintf('%s does not support filters', self::class));
		}

		return $target->getContent();
	}

}
