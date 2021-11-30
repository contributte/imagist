# Nette processor

- [Standalone](#standalone)
- [Nette](#nette)

## Standalone

```php
use Contributte\Imagist\Bridge\Nette\Filter\NetteOperationProcessor;
use Contributte\Imagist\Bridge\Nette\Filter\NetteResourceFactory;
use Contributte\Imagist\Filter\FilterProcessor;

$processor = new FilterProcessor(new NetteResourceFactory(), [
    new NetteOperationProcessor(),
]);
```

## Nette
Nette filters are enabled by default.
