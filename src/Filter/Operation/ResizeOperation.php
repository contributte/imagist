<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Operation;

use Contributte\Imagist\Filter\FilterIdentifier;
use Nette\Utils\Image;

final class ResizeOperation extends OperationAsFilter
{

	/** @var int|string|null */
	private $width;

	/** @var int|string|null */
	private $height;

	private ?string $mode;

	/**
	 * @param int|string|null $width
	 * @param int|string|null $height
	 */
	public function __construct($width, $height, ?string $mode = null)
	{
		$this->width = $width;
		$this->height = $height;
		$this->mode = $mode;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier('resize', [$this->width, $this->height, $this->mode]);
	}

	/**
	 * @return int|string|null
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * @return int|string|null
	 */
	public function getHeight()
	{
		return $this->height;
	}

	public function getMode(): ?string
	{
		return $this->mode;
	}

	/**
	 * @return array{int, int}
	 */
	public function calculateWithMode(int $srcWidth, int $srcHeight): array
	{
		/** @var array{int, int} $calculated */
		$calculated = Image::calculateSize(
			$srcWidth,
			$srcHeight,
			$this->width,
			$this->height,
			$this->getIntMode($this->mode)
		);

		return $calculated;
	}

	/**
	 * @return ResizeOperation[]
	 */
	public function getOperations(): array
	{
		return [$this];
	}

	private function getIntMode(?string $mode): int
	{
		switch ($mode) {
			case 'fill':
				return Image::FILL;
			case 'exact':
				return Image::EXACT;
			case 'shrink_only':
				return Image::SHRINK_ONLY;
			case 'stretch':
				return Image::STRETCH;
			default:
				return Image::FIT;
		}
	}

}
