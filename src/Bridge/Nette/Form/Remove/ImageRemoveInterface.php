<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Remove;

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use Nette\Utils\Html;

interface ImageRemoveInterface
{

	public function getWrapperPart(): Html;

	public function getLabelPart(): Html;

	public function getControlPart(): Html;

	public function getHttpData(ImageUploadControl $input): bool;

	public function getHtml(ImageUploadControl $input): ?Html;

}
