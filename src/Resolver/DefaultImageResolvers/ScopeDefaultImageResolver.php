<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\DefaultImageResolvers;

use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\Resolver\DefaultImageResolverInterface;
use Contributte\Imagist\Utility\RecursionGuard;

final class ScopeDefaultImageResolver implements DefaultImageResolverInterface
{

	use RecursionGuard;

	/** @var string[] */
	private array $lookup;

	/**
	 * @param string[] $lookup
	 */
	public function __construct(array $lookup)
	{
		$this->lookup = $lookup;
	}

	/**
	 * @inheritDoc
	 */
	public function resolve(
		LinkGeneratorInterface $linkGenerator,
		?PersistentImageInterface $image,
		array $options = []
	): ?string
	{
		if ($this->isRecursion($options)) {
			return null;
		}

		$default = $options['scope'] ?? $this->getScopeFromImage($image);

		if (!$default) {
			return null;
		}

		if (!isset($this->lookup[$default])) {
			return null;
		}

		$result = new PersistentImage($this->lookup[$default]);
		if ($image && $image->hasFilter()) {
			$result = $result->withFilterObject($image->getFilter());
		}

		return $linkGenerator->link($result, $this->setRecursion($options));
	}

	private function getScopeFromImage(?PersistentImageInterface $image): ?string
	{
		if (!$image) {
			return null;
		}

		return $image->getScope()->toNullableString();
	}

}
