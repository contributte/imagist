<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Operation;

use Contributte\Imagist\Filter\FilterIdentifier;
use Nette\Utils\Image;

final class CropOperation extends OperationAsFilter
{

	/** @var int|string */
	private $left;

	/** @var int|string */
	private $top;

	/** @var int|string */
	private $width;

	/** @var int|string */
	private $height;

	/**
	 * @param int|string $left
	 * @param int|string $top
	 * @param int|string $width
	 * @param int|string $height
	 */
	public function __construct($left, $top, $width, $height)
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

	/**
	 * @return int|string
	 */
	public function getTop()
	{
		return $this->top;
	}

	/**
	 * @return int|string
	 */
	public function getLeft()
	{
		return $this->left;
	}

	/**
	 * @return int|string
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @return int|string
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * @return array{int, int, int, int}
	 */
	public function calculate(int $srcWidth, int $srcHeight)
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
