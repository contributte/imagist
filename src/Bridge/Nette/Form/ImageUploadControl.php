<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Nette\Form;

use Contributte\Imagist\Bridge\Nette\Exceptions\InvalidArgumentException;
use Contributte\Imagist\Bridge\Nette\Form\Entity\UploadControlEntity;
use Contributte\Imagist\Bridge\Nette\Form\Preview\ImagePreviewInterface;
use Contributte\Imagist\Bridge\Nette\Form\Remove\ImageRemoveInterface;
use Contributte\Imagist\Bridge\Nette\Uploader\FileUploadUploader;
use Contributte\Imagist\Entity\PersistentImage;
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\Scope\Scope;
use LogicException;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;
use Nette\Forms\Helpers;
use Nette\Forms\Validator;
use Nette\Http\FileUpload;
use Nette\Utils\Html;
use Stringable;

final class ImageUploadControl extends BaseControl
{

	private UploadControlEntity $entity;

	private Html $containerPart;

	private ?Scope $scope = null;

	private ?ImagePreviewInterface $preview = null;

	private ?ImageRemoveInterface $remove = null;

	private ?FileUpload $uploadValue = null;

	public function __construct(string|Stringable|null $label = null)
	{
		$this->entity = new UploadControlEntity();
		$this->containerPart = Html::el('div', ['class' => 'image-upload-container']);

		parent::__construct($label);

		$this->control->type = 'file';
		$this->setOption('type', 'file');
		$this->addCondition(true) // not to block the export of rules to JS
			->addRule($this->isOk(...), Validator::$messages[UploadControl::Valid]);
		$this->addRule(sprintf('%s::%s', self::class, 'validateFileSize'), Validator::$messages[Form::MaxFileSize], Helpers::iniGetSize('upload_max_filesize'));

		$this->monitor(Form::class, function (Form $form): void {
			if (!$form->isMethod('post')) {
				throw new InvalidArgumentException('File upload requires method POST.');
			}

			$form->getElementPrototype()->enctype = 'multipart/form-data';
		});
	}

	public static function validateFileSize(ImageUploadControl $control, int|float $limit): bool
	{
		$file = $control->getUploadValue();

		if ($file === null) {
			return true;
		}

		if ($file->getSize() > $limit || $file->getError() === UPLOAD_ERR_INI_SIZE) {
			return false;
		}

		return true;
	}

	public function isFilled(): bool
	{
		return (bool) $this->value;
	}

	private function isOk(): bool
	{
		return match (true) {
			!$this->uploadValue => false,
			default => $this->uploadValue->isOk(),
		};
	}

	public function loadHttpData(): void
	{
		$value = $this->getHttpData(Form::DataFile);

		if (!$value instanceof FileUpload && $value !== null) {
			throw new LogicException(sprintf('Value must be %s, %s given', FileUpload::class, get_debug_type($value)));
		}

		$this->uploadValue = $value;

		$this->setValue($value);

		if ($this->remove) {
			$removeAnyway = $this->remove->getHttpData($this);

			if ($removeAnyway) {
				$this->entity = $this->entity->withRemoveAnyway($removeAnyway);
			}
		}
	}

	/**
	 * @return static
	 */
	public function setScope(?Scope $scope): static
	{
		$this->scope = $scope;

		return $this;
	}

	/**
	 * @return static
	 */
	public function setValue(mixed $value): static
	{
		if ($value === null) {
			$this->entity = $this->entity->withValue();
		} elseif ($value instanceof FileUpload) {
			$image = null;
			if ($value->isOk() && $value->isImage()) {
				$image = new StorableImage(new FileUploadUploader($value), $value->getSanitizedName());

				if ($this->scope) {
					$image = $image->withScope($this->scope);
				}
			}

			$this->entity = $this->entity->withValue(
				$image
			);
		} elseif ($value instanceof PersistentImageInterface) {
			$this->entity = $this->entity
				->withDefault($value)
				->withValue(null);
		} elseif (is_string($value)) {
			$this->entity = $this->entity
				->withDefault(new PersistentImage($value))
				->withValue(null);
		} else {
			$type = is_object($value) ? $value::class : gettype($value);

			throw new InvalidArgumentException(
				sprintf('Value must be %s|%s|string|null, %s given', FileUpload::class, PersistentImageInterface::class, $type)
			);
		}

		return $this;
	}

	public function getValue(): UploadControlEntity
	{
		return $this->entity;
	}

	public function getUploadValue(): ?FileUpload
	{
		return $this->uploadValue;
	}

	public function getControlPart(): ?Html
	{
		$control = parent::getControl();

		if (is_string($control)) {
			throw new LogicException(
				sprintf('Method %s::getControl() returns string instead of HTML object', parent::class)
			);
		}

		return $control;
	}

	public function getContainerPart(): Html
	{
		return $this->containerPart;
	}

	public function getRemovePart(): ?ImageRemoveInterface
	{
		if (!$this->remove) {
			return null;
		}

		return $this->remove;
	}

	public function getPreviewPart(): ?ImagePreviewInterface
	{
		if (!$this->preview) {
			return null;
		}

		return $this->preview;
	}

	public function hasPreviewImage(): bool
	{
		if (!$this->preview) {
			return false;
		}

		return $this->preview->hasImage($this);
	}

	public function getControl(): Html
	{
		$container = clone $this->containerPart;

		if ($preview = $this->getPreviewPart()) {
			if ($previewHtml = $preview->getHtml($this)) {
				$container->insert(null, $previewHtml);
			}
		}

		if ($remove = $this->getRemovePart()) {
			if ($removeHtml = $remove->getHtml($this)) {
				$container->insert(null, $removeHtml);
			}
		}

		return $container->insert(null, parent::getControl());
	}

	/**
	 * @return static
	 */
	public function setPreview(?ImagePreviewInterface $preview): static
	{
		$this->preview = $preview;

		return $this;
	}

	/**
	 * @return static
	 */
	public function setRemove(?ImageRemoveInterface $remove): static
	{
		$this->remove = $remove;

		return $this;
	}

}
