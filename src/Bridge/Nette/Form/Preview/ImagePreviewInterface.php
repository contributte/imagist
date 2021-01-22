<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Preview;

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Filter\FilterInterface;
use Nette\Utils\Html;

interface ImagePreviewInterface
{

	/**
	 * @return static
	 */
	public function setPlaceholder(?PersistentImageInterface $placeholder);

	/**
	 * @return static
	 */
	public function setFilterObject(?FilterInterface $filter);

	/**
	 * @param mixed[] $options
	 * @return static
	 */
	public function setFilter(string $name, array $options = []);

	/**
	 * @phpstan-return Html<Html|string>|null
	 */
	public function getHtml(ImageUploadControl $input): ?Html;

	public function hasImage(ImageUploadControl $input): bool;

}
