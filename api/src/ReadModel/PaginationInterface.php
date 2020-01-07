<?php

declare(strict_types=1);

namespace Api\ReadModel;


interface PaginationInterface
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    public function itemsPerPage(): int;

    public function offset(): int;

    public function sortBy(): array;

    public function sortDesc(): array;

    public function page(): int;

    public function hasSort(): bool;

    public function orderByAttr(string $attr): string;
}
