<?php

declare(strict_types=1);

namespace App\Framework;

interface PaginationInterface
{
    public const SORT_ASC = 'ASC';
    public const SORT_DESC = 'DESC';

    public function itemsPerPage(): int;

    public function offset(): int;

    public function sortBy(): array;

    public function sortDesc(): array;

    public function page(): int;

    public function hasSort(): bool;

    public function orderByAttr(string $attr): string;
}
