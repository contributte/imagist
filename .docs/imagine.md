# Imagine

- [Standalone](#standalone)
- [Nette](#nette)

## Standalone

`composer require imagine/imagine`

```php
use Contributte\Imagist\Bridge\Imagine\ImagineOperationProcessor;
use Contributte\Imagist\Bridge\Imagine\ImagineResourceFactory;
use Contributte\Imagist\Filter\FilterProcessor;

$processor = new FilterProcessor(new ImagineResourceFactory(), [
    new ImagineOperationProcessor(),
]);
```

## Nette

`composer require imagine/imagine`

```neon
imagist:
	extensions:
		imagine:
			enabled: true
		nette:
			enabled: false
```

