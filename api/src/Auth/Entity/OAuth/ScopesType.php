<?php

declare(strict_types=1);

namespace App\Auth\Entity\OAuth;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class ScopesType extends JsonType
{
    public const NAME = 'oauth_scopes';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $data = array_map(function (ScopeEntity $entity) {
            return $entity->getIdentifier();
        }, $value);

        return parent::convertToDatabaseValue($data, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $values = parent::convertToPHPValue($value, $platform);

        if ($values) {
            return array_map(function ($value) {
                return new ScopeEntity($value);
            }, $values);
        }

        return [];
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
