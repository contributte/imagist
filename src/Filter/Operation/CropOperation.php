<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Operation;

use Contributte\Imagist\Filter\FilterIdentifier;
use Nette\Utils\Image;

final class CropOperation extends OperationAsFilter
{

	private int|string $left;

	private int|string $top;

	private int|string $width;

	private int|string $height;

	public function __construct(int|string $left, int|string $top, int|string $width, int|string $height)
	{
		$this->left = $left;
		$this->top = $top;
		$this->width = $width;
		$this->height = $height;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier('crop', [$this->left, $this->top, $this->width, $this->height]);
	}

	public function getTop(): int|string
	{
		return $this->top;
	}

	public function getLeft(): int|string
	{
		return $this->left;
	}

	public function getWidth(): int|string
	{
		return $this->width;
	}

	public function getHeight(): int|string
	{
		return $this->height;
	}

	/**
	 * @return array{int, int, int, int}
	 */
	public function calculate(int $srcWidth, int $srcHeight): array
	{
		/** @var array{int, int, int, int} $calculated */
		$calculated = Image::calculateCutout(
			$srcWidth,
			$srcHeight,
			$this->left,
			$this->top,
			$this->width,
			$this->height
		);

		return $calculated;
	}

}
