<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Filter\Exceptions;

use Contributte\Imagist\Bridge\Nette\Tracy\BlueScreen\ExceptionImageProviderInterface;
use Contributte\Imagist\Entity\ImageInterface;
use Exception;

class OperationNotFoundException extends Exception implements ExceptionImageProviderInterface
{

	private ImageInterface $image;

	public function __construct(ImageInterface $image)
	{
		$this->image = $image;

		$filterName = '';
		$filter = $image->getFilter();
		if ($filter) {
			$filterName = $filter->getName();
		}

		parent::__construct(
			sprintf('Operation not found for image "%s" with filter "%s".', $image->getName(), $filterName)
		);
	}

	public function getImage(): ImageInterface
	{
		return $this->image;
	}

}
