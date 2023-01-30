# Nette

- [Registration](#registration)
- [Configuration](#configuration)
- [Forms](#forms)
- [Latte](#latte)
- [Tracy](#tracy)

## Registration
Register extension in neon

```yaml
extensions:
  imagist: Contributte\Imagist\Bridge\Nette\DI\ImagistExtension
```

## Configuration

```neon
imagist:
    extensions:
        doctrine:
            # Auto-remove images in entity implementing `DoctrineImageRemover`
            removeEvent: false
            # Auto-persist images in entity implementing `DoctrineImagePersister`
            persistEvent: false
            # Doctrine db types
            types:
                # object PersistentImage is created when php retrives id from database
                - class: Contributte\Imagist\Entity\PersistentImage
                # Usage in doctrine: #[Column(type: 'image')]
                  name: image
                  databaseName: db_image
    tracy:
        # Show 'Imagist(1)' instead of '1'
        tabWithName: false
    registration:
        # Loads default image persisters
        persisters: true
        # Loads default image persisters
        removers: true
    # Absolute path to the root of public (www) directory
    baseDir: %wwwDir%
```

## Tracy

Package provides tracy debug bar with bluescreen extension

![tracy](https://raw.githubusercontent.com/contributte/imagist/master/.docs/img/tracy.png)
