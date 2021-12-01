## Standalone

The simplest way is using builder:

```php
use Contributte\Imagist\Builder\LocalImageStorageBuilder;

$builder = new LocalImageStorageBuilder(__DIR__ . '/path/to/www/dir');
$builder->withImagineFilterProcessor();
$result = $builder->build();

$result->getImageStorage();
$result->getLinkGenerator();
$result->getTransactionFactory();
```

For advanced use please go to source of `Contributte\Imagist\Builder\LocalImageStorageBuilder::build()`
