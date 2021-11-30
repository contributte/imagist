<?php declare(strict_types = 1);

namespace Contributte\Imagist\Filter\Operation;

use Contributte\Imagist\Filter\FilterIdentifier;

final class QualityOperation extends OperationAsFilter implements SilentOperationInterface
{

	private int $quality;

	public function __construct(int $quality)
	{
		$this->quality = $quality;
	}

	public function getIdentifier(): FilterIdentifier
	{
		return new FilterIdentifier('crop', [$this->quality]);
	}

	public function getQuality(): int
	{
		return $this->quality;
	}

}
