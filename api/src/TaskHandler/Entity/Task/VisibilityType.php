<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class VisibilityType extends StringType
{
    public const NAME = 'task_handler_task_visibility';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Visibility ? $value->getName() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Visibility
    {
        return !empty($value) ? new Visibility((string)$value) : null;
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
