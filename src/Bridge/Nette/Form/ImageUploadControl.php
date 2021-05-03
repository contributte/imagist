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
use Nette\Application\UI;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Form;
use Nette\Forms\Helpers;
use Nette\Forms\Rule;
use Nette\Forms\Validator;
use Nette\Http\FileUpload;
use Nette\InvalidStateException;
use Nette\Utils\Html;

final class ImageUploadControl extends UploadControl
{

	private UploadControlEntity $entity;

	private ?FileUpload $fileUpload = null;

	private ?int $maxFileSize = null;

	private ?string $maxFileSizeMessage = null;

	private ?Scope $scope = null;

	private ?ImagePreviewInterface $preview = null;

	private ?ImageRemoveInterface $remove = null;

	public function __construct(?string $label = null)
	{
		$this->entity = new UploadControlEntity();

		BaseControl::__construct($label);

		$this->control->type = 'file';
		$this->setOption('type', 'file');
		$this->addRule([$this, 'isOk'], Validator::$messages[self::VALID]);

		$this->monitor(
			Form::class,
			function (Form $form): void {
				if (!$form->isMethod('post')) {
					throw new InvalidStateException('File upload requires method POST.');
				}

				$form->getElementPrototype()->enctype = 'multipart/form-data';

				if ($form->isAnchored()) {
					$this->formAnchored();
				} elseif ($form instanceof UI\Form) {
					$form->onAnchor[] = function (): void {
						$this->formAnchored();
					};
				} else {
					throw new InvalidStateException(
						sprintf('Form is not anchored or is not instance of %s', UI\Form::class)
					);
				}
			}
		);
	}

	public function loadHttpData(): void
	{
		parent::loadHttpData();

		$this->setValue($this->value);

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
	public function setScope(?Scope $scope)
	{
		$this->scope = $scope;

		return $this;
	}

	/**
	 * @return static
	 */
	public function setMaxFileSize(int $bytes, ?string $message = null)
	{
		$form = $this->getForm(false);
		if ($form && $form->isAnchored()) {
			throw new InvalidStateException('setMaxFileSize method must be called before form is anchored');
		}

		$this->maxFileSize = $bytes;
		$this->maxFileSizeMessage = $message;

		return $this;
	}

	/**
	 * @param FileUpload|PersistentImageInterface|string|null $value
	 * @return static
	 */
	public function setValue($value)
	{
		if ($value === null) {
			$this->entity = $this->entity->withValue(null);
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
			$this->fileUpload = $value;
		} elseif ($value instanceof PersistentImageInterface) {
			$this->entity = $this->entity
				->withDefault($value)
				->withValue(null);
		} elseif (is_string($value)) {
			$this->entity = $this->entity
				->withDefault(new PersistentImage($value))
				->withValue(null);
		} else {
			// @phpstan-ignore-next-line $value is mixed
			$type = is_object($value) ? get_class($value) : gettype($value);

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

	/**
	 * @phpstan-return Html<Html|string>|null
	 */
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

	/**
	 * @phpstan-return Html<Html|string>|null
	 */
	public function getRemovePart(): ?Html
	{
		if (!$this->remove) {
			return null;
		}

		return $this->remove->getHtml($this);
	}

	/**
	 * @phpstan-return Html<Html|string>|null
	 */
	public function getPreviewPart(): ?Html
	{
		if (!$this->preview) {
			return null;
		}

		return $this->preview->getHtml($this);
	}

	public function hasPreviewImage(): bool
	{
		if (!$this->preview) {
			return false;
		}

		return $this->preview->hasImage($this);
	}

	/**
	 * @phpstan-return Html<Html|string>
	 */
	public function getControl(): Html
	{
		$container = Html::el(
			'div',
			[
				'class' => ['image-upload-container'],
			]
		);

		if ($preview = $this->getPreviewPart()) {
			$container->insert(null, $preview);
		}

		if ($remove = $this->getRemovePart()) {
			$container->insert(null, $remove);
		}

		return $container->insert(null, parent::getControl());
	}

	/**
	 * @return static
	 */
	public function setPreview(?ImagePreviewInterface $preview)
	{
		$this->preview = $preview;

		return $this;
	}

	/**
	 * @return static
	 */
	public function setRemove(?ImageRemoveInterface $remove)
	{
		$this->remove = $remove;

		return $this;
	}

	private function formAnchored(): void
	{
		$this->addRule(
			fn(ImageUploadControl $control, int $limit): bool => $this->validateMaxFileSize($limit),
			$this->maxFileSizeMessage ?? Validator::$messages[Form::MAX_FILE_SIZE],
			$this->maxFileSize ?? Helpers::iniGetSize('upload_max_filesize')
		);

		/** @var Rule $rule */
		foreach ($this->getRules() as $rule) {
			if ($rule->validator === Form::MAX_FILE_SIZE) {
				throw new LogicException(
					sprintf(
						'Cannot use ->addRule(Form::MAX_FILE_SIZE) in %s, use ->setMaxFileSize() instead',
						self::class
					)
				);
			}
		}
	}

	private function validateMaxFileSize(int $limit): bool
	{
		if (!$this->fileUpload || !$this->fileUpload->isOk()) {
			return true;
		}

		return $this->fileUpload->getSize() <= $limit && $this->fileUpload->getError() !== UPLOAD_ERR_INI_SIZE;
	}

}
