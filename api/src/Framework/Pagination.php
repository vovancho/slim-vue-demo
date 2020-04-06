<?php

declare(strict_types=1);

namespace App\Framework;

use Psr\Http\Message\ServerRequestInterface;

class Pagination implements PaginationInterface
{
    private int $itemsPerPage;
    private int $page;
    private array $sortBy;
    private array $sortDesc;

    public function __construct(int $itemsPerPage, int $page, array $sortBy, array $sortDesc)
    {
        $this->itemsPerPage = $itemsPerPage < 1 ? 10 : $itemsPerPage;
        $this->page = $page < 1 ? 1 : $page;
        $this->sortBy = $sortBy;
        $this->sortDesc = $sortDesc;
    }

    public static function createByRequest(ServerRequestInterface $request): PaginationInterface
    {
        $params = $request->getQueryParams();
        return new self(
            (int)($params['itemsPerPage'] ?? 10),
            (int)($params['page'] ?? 1),
            $params['sortBy'] ?? [],
            $params['sortDesc'] ?? []
        );
    }

    public function itemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function offset(): int
    {
        $itemsPerPage = $this->itemsPerPage();
        return $itemsPerPage < 1 ? 0 : $this->page() * $itemsPerPage - $itemsPerPage;
    }

    public function sortBy(): array
    {
        return $this->sortBy;
    }

    public function sortDesc(): array
    {
        return $this->sortDesc;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function hasSort(): bool
    {
        return count($this->sortBy) > 0;
    }

    public function orderByAttr(string $attr): string
    {
        $index = array_search($attr, $this->sortBy());
        $sortDescByAttr = $this->sortDesc()[$index];
        return !empty($sortDescByAttr) && $sortDescByAttr === 'true' ? self::SORT_DESC : self::SORT_ASC;
    }
}
