<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Gumlet;

use Contributte\Imagist\Context\Context;
use Contributte\Imagist\Entity\EmptyImageInterface;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\FilterNormalizerProcessorInterface;
use Contributte\Imagist\LinkGeneratorInterface;
use Contributte\Imagist\PathInfo\PathInfoFactoryInterface;
use Contributte\Imagist\Resolver\DefaultImageResolverInterface;

final class GumletLinkGenerator implements LinkGeneratorInterface
{

	public const DEFAULT_DOMAIN = 'gumlet.io';
	public const GUMLET_CONTEXT_KEY = 'gumlet';

	private string $domain = self::DEFAULT_DOMAIN;

	private string $bucket;

	private ?string $token;

	private PathInfoFactoryInterface $pathInfoFactory;

	private DefaultImageResolverInterface $defaultImageResolver;

	private FilterNormalizerProcessorInterface $filterNormalizer;

	public function __construct(string $bucket, ?string $token, PathInfoFactoryInterface $pathInfoFactory, DefaultImageResolverInterface $defaultImageResolver, FilterNormalizerProcessorInterface $filterNormalizer)
	{
		$this->bucket = $bucket;
		$this->token = $token;
		$this->pathInfoFactory = $pathInfoFactory;
		$this->defaultImageResolver = $defaultImageResolver;
		$this->filterNormalizer = $filterNormalizer;
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
		$path = $pathInfo->toString($pathInfo::BUCKET | $pathInfo::SCOPE | $pathInfo::IMAGE);

		$options = $this->filterNormalizer->normalize($image, new Context([
			self::GUMLET_CONTEXT_KEY => true,
		]));

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
