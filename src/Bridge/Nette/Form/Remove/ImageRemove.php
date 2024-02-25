<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form\Remove;

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use LogicException;
use Nette\Forms\Form;
use Nette\Utils\Html;

class ImageRemove implements ImageRemoveInterface
{

	private Html $wrapperPart;

	private Html $labelPart;

	private Html $controlPart;

	private string $caption;

	public function __construct(string $caption)
	{
		$this->caption = $caption;

		$this->wrapperPart = Html::el('div', ['class' => 'image-upload-remove-wrapper']);
		$this->labelPart = Html::el('label');
		$this->controlPart = Html::el('input', ['type' => 'checkbox']);
	}

	public function getWrapperPart(): Html
	{
		return $this->wrapperPart;
	}

	public function getLabelPart(): Html
	{
		return $this->labelPart;
	}

	public function getControlPart(): Html
	{
		return $this->controlPart;
	}

	public function getHttpData(ImageUploadControl $input): bool
	{
		$form = $input->getForm();
		assert($form !== null);

		return (bool) $form->getHttpData(Form::DataText, $input->getName() . '_remove');
	}

	public function getHtml(ImageUploadControl $input): ?Html
	{
		if ($input->isRequired()) {
			throw new LogicException('Cannot use remove in required input');
		}

		if (!$input->getValue()->getDefault()) {
			return null;
		}

		$wrapper = clone $this->wrapperPart;
		$label = clone $this->labelPart;
		$control = clone $this->controlPart;

		$control->setAttribute('id', $input->getHtmlId() . '_remove');
		$control->setAttribute('name', $input->getName() . '_remove');
		$control->setAttribute('checked', $this->getHttpData($input));

		$label->insert(null, $control);
		$label->create('')->setText(' ' . $this->caption);

		$wrapper->insert(null, $label);

		return $wrapper;
	}

}
