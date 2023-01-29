# Deleting

Only persisted image can be deleted.

```php
use Contributte\Imagist\Entity\PersistentImageInterface;use Contributte\Imagist\ImageStorageInterface;

/** @var PersistentImageInterface $image */
$image;
/** @var ImageStorageInterface $imageStorage */
$imageStorage;

$imageStorage->remove($image);
```
