<?php declare(strict_types = 1);

namespace Contributte\Imagist\PathInfo;

/**
 * %publicDir%/media-bucket/scope/_filter/imageName.png
 *            \-----------/\-----/\------/\-----------/
 *                bucket scope filter imageName
 */
interface PathInfoInterface
{

	public const BUCKET = 0b0001;
	public const SCOPE = 0b0010;
	public const FILTER = 0b0100;
	public const IMAGE = 0b1000;
	public const ALL = 0b1111;

	public function toString(int $parts = self::ALL): string;

}
