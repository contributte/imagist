# Nette Forms

- [Registration](#registration)
- [Default value](#default-value)
- [Image preview](#image-preview)
- [Image remove checkbox](#image-remove-checkbox)

## Registration

The best way is to create a new method in `Nette\Application\UI\Form`

```php

use Contributte\Imagist\Bridge\Nette\Form\ImageUploadControl;
use Contributte\Imagist\Scope\Scope;

class Form extends \Nette\Application\UI\Form
{

    public function addImageUpload(string $name, ?string $label = null, ?string $scope = null): ImageUploadControl
	{
		$control = $this[$name] = new ImageUploadControl($label);
		if ($scope) {
			$control->setScope(new Scope($scope));
		}

		return $control;
	}

}

// usage
$form = new Form();

$form->addImageUpload('image');
```

if you do not want to register a new method, create new instance

```php
$form = new Form();

$form['image'] = new ImageUploadControl($label);
```

## Default Value

```php
use Contributte\Imagist\Entity\PersistentImage;

$form = new Form();

$form->addImageUpload('image')
    ->setDefaultValue(new PersistentImage('image.png'));

// or

$form->setDefaults([
    'image' => new PersistentImage('image.png'),
]);
```

## Image Preview

```php
use Contributte\Imagist\Entity\PersistentImage;

assert($imagePreviewFactory instanceof Contributte\Imagist\Bridge\Nette\Form\Preview\ImagePreviewFactoryInterface); // inject

$form = new Form();

$form->addImageUpload('image')
    ->setPreview($imagePreviewFactory->create())
    ->setDefaultValue(new PersistentImage('image.png'));
```

## Image Remove Checkbox

```php
use Contributte\Imagist\Bridge\Nette\Form\Remove\ImageRemove;
use Contributte\Imagist\Entity\PersistentImage;

$form = new Form();

$form->addImageUpload('image')
    ->setRemove(new ImageRemove('Check to delete image'))
    ->setDefaultValue(new PersistentImage('image.png'));
```
