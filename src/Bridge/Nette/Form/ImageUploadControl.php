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
use Nette\Forms\Controls\UploadControl;
use Nette\Http\FileUpload;
use Nette\Utils\Html;

final class ImageUploadControl extends UploadControl
{

	private UploadControlEntity $entity;

	private Html $containerPart;

	private ?Scope $scope = null;

	private ?ImagePreviewInterface $preview = null;

	private ?ImageRemoveInterface $remove = null;

	public function __construct(?string $label = null)
	{
		$this->entity = new UploadControlEntity();
		$this->containerPart = Html::el('div', ['class' => 'image-upload-container']);

		parent::__construct($label);
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

}
