<?php


namespace Api\Model\Base;


use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Uuid1
{
    private $id;

    public function __construct(string $id)
    {
        Assert::notEmpty($id);
        $this->id = $id;
    }

    public static function next(): self
    {
        return new self(Uuid::uuid1()->toString());
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
