# Scopes

In almost every website we have many places where we have to work with images e.g. users, articles, products etc.
For better orientation, structure and manipulation there are scopes. The only place where we define the scope is the image upload.

```php
use Contributte\Imagist\ImageStorageInterface;
use Contributte\Imagist\Scope\Scope;

/** @var ImageStorageInterface $imageStorage */
$imageStorage;

$image = $imageStorage->persist(new StorableImage(scope: new Scope('avatars')));
```

now we don't mix article images with user images and have easier manipulation in `FileNameResolverInterface`, `ImagePersisterInterface`, `DefaultImageResolverInterface`, etc.

By default, scopes followed folder hierarchy, scope `avatars` is saved into `rootDir/avatars/*`, scope `articles` into `rootDir/articles/*`.
It can be changed in `PathInfoFactoryInterface`.

## Levels

Scopes can have more than one level:

```php
$johnScope = new Scope('avatars', 'john');
$jamesScope = new Scope('avatars', 'james');
```
