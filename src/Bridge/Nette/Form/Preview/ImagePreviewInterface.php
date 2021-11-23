<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Preview;

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use Contributte\Imagist\Entity\Filter\ImageFilter;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Nette\Utils\Html;

interface ImagePreviewInterface
{

	public function getWrapperPart(): Html;

	public function getImagePart(): Html;

	/**
	 * @return static
	 */
	public function setPlaceholder(?PersistentImageInterface $placeholder);

	/**
	 * @return static
	 */
	public function setFilterObject(?ImageFilter $filter);

	/**
	 * @param mixed[] $options
	 * @return static
	 */
	public function setFilter(string $name, array $options = []);

	public function getHtml(ImageUploadControl $input): ?Html;

	public function hasImage(ImageUploadControl $input): bool;

}
