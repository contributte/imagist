<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Preview;

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Nette\Utils\Html;

interface ImagePreviewInterface
{

	public function getWrapperPart(): Html;

	public function getImagePart(): Html;

	/**
	 * @return static
	 */
	public function setPlaceholder(?PersistentImageInterface $placeholder): static;

	/**
	 * @return static
	 */
	public function setFilter(FilterInterface $filter): static;

	public function getHtml(ImageUploadControl $input): ?Html;

	public function hasImage(ImageUploadControl $input): bool;

}
