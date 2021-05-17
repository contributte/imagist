<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet;

use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\FilterNormalizerCollectionInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\PathInfo\PathInfoFactoryInterface;
use Contributte\Imagist\Resolver\DefaultImageResolverInterface;

final class GumletLinkGenerator implements LinkGeneratorInterface
{

	public const GUMLET_CONTEXT_KEY = 'gumlet';

	private ?string $token;

	private string $bucket;

	private string $domain = 'gumlet.io';

	private FilterNormalizerCollectionInterface $normalizerCollection;

	private PathInfoFactoryInterface $pathInfoFactory;

	private DefaultImageResolverInterface $defaultImageResolver;

	public function __construct(
		string $bucket,
		?string $token,
		FilterNormalizerCollectionInterface $normalizerCollection,
		PathInfoFactoryInterface $pathInfoFactory,
		DefaultImageResolverInterface $defaultImageResolver
	)
	{
		$this->bucket = $bucket;
		$this->token = $token;
		$this->normalizerCollection = $normalizerCollection;
		$this->pathInfoFactory = $pathInfoFactory;
		$this->defaultImageResolver = $defaultImageResolver;
	}

	public function setDomain(string $domain): void
	{
		$this->domain = $domain;
	}

	/**
	 * @param mixed[] $options
	 */
	public function link(?PersistentImageInterface $image, array $options = []): ?string
	{
		if (!$image || $image instanceof EmptyImageInterface) {
			return $this->defaultImageResolver->resolve($this, $image, $options);
		}

		return sprintf('https://%s.%s/%s', $this->bucket, $this->domain, $this->createPath($image));
	}

	private function createPath(PersistentImageInterface $image): string
	{
		$pathInfo = $this->pathInfoFactory->create($image->getOriginal());
		$path = $pathInfo->toString($pathInfo::ALL & ~$pathInfo::FILTER);

		$options = $this->normalizerCollection->normalize($image, [
			self::GUMLET_CONTEXT_KEY => true,
		]);

		if (!$options) {
			return $this->hashIfNeed($path, true);
		}

		return $this->hashIfNeed($path . '?' . http_build_query($options), false);
	}

	private function hashIfNeed(string $path, bool $questionMark): string
	{
		if (!$this->token) {
			return $path;
		}

		return $path . ($questionMark ? '?' : '&') . 's=' . md5($this->token . '/' . $path);
	}

}
