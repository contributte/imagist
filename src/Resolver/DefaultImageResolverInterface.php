<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver;

use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\LinkGeneratorInterface;

interface DefaultImageResolverInterface
{

	/**
	 * @param mixed[] $options
	 */
	public function resolve(
		LinkGeneratorInterface $linkGenerator,
		?PersistentImageInterface $image,
		array $options = []
	): ?string;

}
