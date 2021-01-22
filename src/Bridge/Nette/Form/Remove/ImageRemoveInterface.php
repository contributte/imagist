<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Remove;

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use Nette\Utils\Html;

interface ImageRemoveInterface
{

	public function getHttpData(ImageUploadControl $input): bool;

	/**
	 * @phpstan-return Html<Html|string>|null
	 */
	public function getHtml(ImageUploadControl $input): ?Html;

}
