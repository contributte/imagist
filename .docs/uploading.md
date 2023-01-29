# Uploading

For uploading images we use `StorableImageInterface` and any class implementing `UploaderInterface`.

Currently, we have 2 implementation of `UploaderInterface`:

- [FilePathUploader](#filepathuploader)
- [StringUploader](#stringuploader)
- [FileUploadUploader](#fileuploaduploader) for nette `FileUpload`
- [UploadedFileUploader](#uploadedfileuploader) for symfony
- [Custom uploader](#custom-uploader) for your own scenario
- [Next Steps](#next-steps)


## FilePathUploader

```php
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\Uploader\FilePathUploader;

/** @var ImageStorageInterface $imageStorage */
$imageStorage;

$image = $imageStorage->persist(new StorableImage(
    new FilePathUploader('path/to/image.png'),
    'nameOfImage.png',
));

$image->getId(); // now image is PersistentImageInterface that has a unique ID
```

## StringUploader

```php
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\Uploader\StringUploader;

/** @var ImageStorageInterface $imageStorage */
$imageStorage;

$image = $imageStorage->persist(new StorableImage(
    new StringUploader(file_get_contents('path/to/image.png')),
    'nameOfImage.png',
));

$image->getId(); // now image is PersistentImageInterface that has a unique ID
```

## FileUploadUploader

```php
use Contributte\Imagist\Bridge\Nette\Uploader\FileUploadUploader;
use Contributte\Imagist\Entity\StorableImage;
use Contributte\Imagist\ImageStorageInterface;
use Nette\Http\FileUpload;

/** @var ImageStorageInterface $imageStorage */
$imageStorage;

$image = $imageStorage->persist(new StorableImage(
    new FileUploadUploader(new FileUpload(/* ... */)),
    'nameOfImage.png',
));

$image->getId(); // now image is PersistentImageInterface that has a unique ID
```

## UploadedFileUploader

TODO

## Custom Uploader

```php

use Contributte\Imagist\Uploader\UploaderInterface;

class ResourceUploader implements UploaderInterface
{

    public function __construct(
        private resource $resource,
    ) {}

    public function getContent(): string
    {
        assert(($contents = stream_get_contents($this->resource)) !== false);

        return $contents;
    }

}

$image->getId(); // now image is PersistentImageInterface that has a unique ID
```

# Next Steps

[Scopes](scopes.md) \
[Persisting and retrieving (database)](persisting-retrieving.md)
