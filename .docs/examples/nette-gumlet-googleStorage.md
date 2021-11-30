# Gumlet + Google Storage

Gumlet is service, that helps to deliver transformed, optimized, resized etc. images to users.
Gumlet reads original images from Google Cloud Storage, Web Folder, Web Proxy, Amazon S3 Bucket, DigitalOcean Spaces Bucket, Wasabi Storage...

Image storage was designed for easy customization, so change will be fast and painless, here we go.

## Google storage - configuration

```neon
extensions:
	image.gumlet: Contributte\Imagist\Bridge\Nette\DI\GumletImageStorageExtension

image.gumlet:
	bucket: string
	token: string|null
```

And that's all. Now we use Google storage instead of default filesystem storage.

```neon
services:
    image.filesystem:
        factory: Contributte\Imagist\Filesystem\GoogleStorageFilesystem('bucket', %appDir%/config/gcs.json)
```

## Gumlet operation processor

```php
final class GumletOperationProcessor implements OperationProcessorInterface
{

	public function process(object $resource, OperationCollection $collection, ContextInterface $context): void
	{
		if (!$resource instanceof ArrayResource) {
			return;
		}

		$builder = new GumletBuilder();

		if ($crop = $collection->get(CropOperation::class)) {
			$builder->extract($crop->getLeft(), $crop->getTop(), $crop->getWidth(), $crop->getHeight());
		}

		$resource->merge($builder->build());
	}

}

```
