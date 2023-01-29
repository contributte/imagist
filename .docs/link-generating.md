# Link generating

When the image is persisted we pass it to the link method of the `LinkGeneratorInterface`.

```php
use Contributte\Imagist\Entity\PersistentImageInterface;
use Contributte\Imagist\LinkGeneratorInterface;

/** @var LinkGeneratorInterface $linkGenerator */
$linkGenerator;
/** @var PersistentImageInterface $image */
$image;

echo $linkGenerator->link($image);
```
