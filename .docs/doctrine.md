# Doctrine

- [Image column type](#image-column-type)
- [Automatically persist and remove image](#automatically-persist-and-remove-image)

## Image column type

Class `Contributte\Imagist\Bridge\Doctrine\ImageType` is auto-registered, takes care of converting PersistentImageInterface
to string (database value) and converting database value to PersistentImageInterface (PHP value). Uses class `Contributte\Imagist\Database\DatabaseConverter`, which can be used standalone.

```php
class Entity
{

	#[Column(type: 'image', nullable: false)]
	protected PersistentImageInterface $image;

}
```

## Automatically persist and remove image

Sometimes we need persist promised image before entity is persisted (e.g. when we use symfony serializer) and always remove image.

Automatically remove image

```php
use Contributte\Imagist\Bridge\Doctrine\Event\DoctrineImageRemover;use Contributte\Imagist\Entity\PersistentImageInterface;

class Entity implements DoctrineImageRemover
{

	#[Column(type: 'image', nullable: true)]
	protected ?PersistentImageInterface $image = null;

    /**
     * @return array<PersistentImageInterface|null>
     */
	public function _imagesToClean(): array
	{
        return [$this->image];
    }

}
```
Automatically persist promised images

```php
use Contributte\Imagist\Bridge\Doctrine\Event\DoctrineImagePersister;

class Entity implements DoctrineImagePersister
{

	#[Column(type: 'image', nullable: true)]
	protected ?PersistentImageInterface $image = null;

    /**
     * @return array<PersistentImageInterface|null>
     */
	public function _promisedImagesToPersist(): array
	{
        return [$this->image];
    }

}
```

By default persister and remover are disabled, enable in neon:
```yaml
imagist:
  extensions:
    doctrine:
      removeEvent: true
      promisedPersistEvent: true
```
