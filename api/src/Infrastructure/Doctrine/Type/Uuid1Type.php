<?php

declare(strict_types=1);

namespace Api\Infrastructure\Doctrine\Type;


use Api\Model\Base\Uuid1;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class Uuid1Type extends GuidType
{
    public const NAME = 'uuid1';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Uuid1 ? $value->getId() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new Uuid1($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
