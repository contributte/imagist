<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\NetteImage;

class NetteImageOptions
{

	private ?int $quality = null;

	public function setQuality(?int $quality): void
	{
		$this->quality = $quality;
	}

	public function getQuality(): ?int
	{
		return $this->quality;
	}

}
