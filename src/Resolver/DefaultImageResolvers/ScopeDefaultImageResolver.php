<?php declare(strict_types = 1);

namespace Contributte\Imagist\Resolver\DefaultImageResolvers;

use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\Resolver\DefaultImageResolverInterface;

final class ScopeDefaultImageResolver implements DefaultImageResolverInterface
{

	private const RECURSION_GUARD = 'scopeDefaultImageRecursion';

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
		if (isset($options[self::RECURSION_GUARD])) {
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
			$result = $result->withFilter($image->getFilter());
		}

		$options[self::RECURSION_GUARD] = true;

		return $linkGenerator->link($result, $options);
	}

	private function getScopeFromImage(?PersistentImageInterface $image): ?string
	{
		if (!$image) {
			return null;
		}

		return $image->getScope()->toNullableString();
	}

}
