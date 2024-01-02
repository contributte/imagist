<?php declare(strict_types = 1);

namespace Contributte\Imagist\Entity;

use Contributte\Imagist\Scope\Scope;
use Contributte\Imagist\Uploader\UploaderInterface;

class StorableImage extends Image implements StorableImageInterface
{

	protected UploaderInterface $uploader;

	public function __construct(UploaderInterface $uploader, string $name, ?Scope $scope = null)
	{
		$this->uploader = $uploader;

		parent::__construct($name, $scope);
	}

	final public function close(?string $reason = null): void
	{
		$this->setClosed($reason);
	}

	public function getUploader(): UploaderInterface
	{
		return $this->uploader;
	}

}
