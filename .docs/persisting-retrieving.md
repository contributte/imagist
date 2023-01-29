# Persisting and retrieving

When image is saved in filesystem we need to save it in database.

`PersistentImageInterface|null` -> `string|null`
```php
use Contributte\Imagist\Database\DatabaseConverter;
use Contributte\Imagist\Entity\PersistentImageInterface;

/** @var PersistentImageInterface $image */
$image;

$converter = new DatabaseConverter(); // exists as service in the framework

/** @var string|null $databaseValue */
$databaseValue = $converter->convertToDatabase($image);
```

`string|null` -> `PersistentImageInterface|null`

```php
use Contributte\Imagist\Database\DatabaseConverter;
use Contributte\Imagist\Entity\PersistentImageInterface;

$converter = new DatabaseConverter(); // exists as service in the framework

/** @var PersistentImageInterface|null $image */
$image = $converter->convertToPhp($valueFromDatabase);
```
