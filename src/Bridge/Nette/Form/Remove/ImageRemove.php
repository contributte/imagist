<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Remove;

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use LogicException;
use Nette\Forms\Form;
use Nette\Utils\Html;

class ImageRemove implements ImageRemoveInterface
{

	private string $caption;

	public function __construct(string $caption)
	{
		$this->caption = $caption;
	}

	public function getHttpData(ImageUploadControl $input): bool
	{
		$form = $input->getForm();
		assert($form !== null);

		return (bool) $form->getHttpData(Form::DATA_TEXT, $input->getName() . '_remove');
	}

	public function getHtml(ImageUploadControl $input): ?Html
	{
		if ($input->isRequired()) {
			throw new LogicException('Cannot use remove in required input');
		}

		if (!$input->getValue()->getDefault()) {
			return null;
		}

		$wrapper = Html::el('div', [
			'class' => ['image-upload-remove-wrapper'],
		]);

		$label = $wrapper->create('label');
		$label->create('input', [
			'type' => 'checkbox',
			'id' => $input->getHtmlId() . '_remove',
			'name' => $input->getName() . '_remove',
			'checked' => $this->getHttpData($input),
		]);
		$label->create('')->setText(' ' . $this->caption);

		return $wrapper;
	}

}
