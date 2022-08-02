<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Latte\Extension;

use Contributte\Imagist\Bridge\Nette\Latte\Extension\Node\ImgNode;
use Contributte\Imagist\Bridge\Nette\Latte\LatteImageProvider;
use Latte\Extension;

final class ImagistExtension extends Extension
{

	public function __construct(
		private LatteImageProvider $provider,
	)
	{
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getProviders(): array
	{
		return [
			'imagist' => $this->provider,
		];
	}

	/**
	 * @return array<string, callable>
	 */
	public function getTags(): array
	{
		return [
			'img' => [ImgNode::class, 'create'],
			'n:img' => [ImgNode::class, 'create'],
		];
	}

}
