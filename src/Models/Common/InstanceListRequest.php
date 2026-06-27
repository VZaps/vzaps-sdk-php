<?php

declare(strict_types=1);

namespace VZaps\Sdk\Models\Common;

final class InstanceListRequest implements VZapsModel
{
    /**
     * @param array<string, mixed> $filter
     */
    public function __construct(
        public readonly int $page = 1,
        public readonly int $size = 20,
        public readonly array $filter = [],
        public readonly ?string $search = null,
        public readonly ?string $sort = null,
        public readonly ?bool $sortDesc = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $filter = $this->filter;
        if ($this->search !== null && trim($this->search) !== '') {
            $filter['query'] = trim($this->search);
        }

        return array_filter([
            'page' => $this->page,
            'size' => $this->size,
            'filter' => $filter,
            'sort' => $this->sort,
            'sortDesc' => $this->sortDesc,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
