<?php declare(strict_types = 1);

namespace Contributte\Imagist\Bridge\Doctrine;

use Contributte\Imagist\Database\DatabaseConverter;
use Contributte\Imagist\Database\DatabaseConverterInterface;
use Contributte\Imagist\Entity\ImageInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;
use LogicException;

class ImageType extends StringType
{

	private const TYPE = 'image';
	private const DB_TYPE = 'db_image';

	private DatabaseConverterInterface $databaseConverter;

	public function getDatabaseConverter(): DatabaseConverterInterface
	{
		if (!isset($this->databaseConverter)) {
			$this->databaseConverter = new DatabaseConverter();
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
	public function getName()
	{
		return 'image';
	}

	/**
	 * @inheritDoc
	 */
	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}

	public static function register(Connection $connection): void
	{
		if (!$connection->getDatabasePlatform()->hasDoctrineTypeMappingFor(self::DB_TYPE)) {
			self::registerType();

			$connection->getDatabasePlatform()->registerDoctrineTypeMapping(self::DB_TYPE, self::TYPE);
		}
	}

	public static function registerType(): void
	{
		if (Type::hasType(self::TYPE)) {
			$class = Type::getTypesMap()[self::TYPE];
			if ($class !== static::class) {
				throw new LogicException(
					sprintf('Doctrine type %s is already registered for class %s', self::TYPE, $class)
				);
			}
		} else {
			Type::addType(self::TYPE, static::class);
		}
	}

}
