<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Doctrine;

use Contributte\Imagist\Database\DatabaseConverter;
use Contributte\Imagist\Database\DatabaseConverterInterface;
use Contributte\Imagist\Entity\ImageInterface;
use Contributte\Imagist\Entity\PersistentImage;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;
use LogicException;

class ImageType extends StringType
{

	private DatabaseConverterInterface $databaseConverter;

	/** @var class-string<PersistentImage> */
	private string $className = PersistentImage::class;

	private string $name = 'image';

	public function getDatabaseConverter(): DatabaseConverterInterface
	{
		if (!isset($this->databaseConverter)) {
			$this->databaseConverter = new DatabaseConverter(true, $this->className);
		}

		return $this->databaseConverter;
	}

	/**
	 * @inheritDoc
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if (!$value instanceof ImageInterface && $value !== null) {
			throw ConversionException::conversionFailedInvalidType(
				$value,
				$this->getName(),
				['null', ImageInterface::class]
			);
		}

		return $this->getDatabaseConverter()->convertToDatabase($value);
	}

	/**
	 * @inheritDoc
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform) // phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.ScopeNotCamelCaps
	{
		if (!is_string($value) && $value !== null) {
			throw ConversionException::conversionFailedInvalidType(
				$value,
				$this->getName(),
				['null', 'string']
			);
		}

		return $this->getDatabaseConverter()->convertToPhp($value);
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @inheritDoc
	 */
	// phpcs:ignore -- is not in camel caps
	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}

	/**
	 * @param class-string<PersistentImage> $className
	 */
	public static function register(Connection $connection, string $name = 'image', string $dbName = 'db_image', string $className = PersistentImage::class): void
	{
		if (!$connection->getDatabasePlatform()->hasDoctrineTypeMappingFor($dbName)) {
			self::registerType($name, $className);

			$connection->getDatabasePlatform()->registerDoctrineTypeMapping($dbName, $name);
		}
	}

	/**
	 * @param class-string<PersistentImage> $className
	 */
	public static function registerType(string $name = 'image', string $className = PersistentImage::class): void
	{
		if (Type::hasType($name)) {
			$class = Type::getTypesMap()[$name];

			if ($class !== static::class) {
				throw new LogicException(
					sprintf('Doctrine type %s is already registered for class %s', $name, $class)
				);
			}
		} else {
			$self = new self();
			$self->name = $name;
			$self->className = $className;

			self::getTypeRegistry()->register($name, $self);
		}
	}

}
