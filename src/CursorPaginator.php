<?php

declare(strict_types=1);

namespace Laravel\Forge;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Generator;
use IteratorAggregate;
use JsonSerializable;

/**
 * @implements IteratorAggregate<int, mixed>
 * @implements ArrayAccess<int, mixed>
 *
 * Note: `(array) $paginator` is intentionally NOT equivalent to `$paginator->toArray()`.
 * PHP's object-to-array cast exposes the paginator's public/protected/private properties
 * (mangling private/protected keys with NUL bytes) rather than the items collection.
 * To obtain just the items, use `->toArray()`, `json_encode($paginator)`, or `foreach`.
 */
class CursorPaginator implements IteratorAggregate, Countable, ArrayAccess, JsonSerializable
{
    /**
     * Create a new CursorPaginator instance.
     */
    public function __construct(
        protected array $items,
        protected ?string $nextCursor,
        protected ?int $perPage,
        protected Forge $forge,
        protected string $uri,
        protected string $class,
        protected ?string $organizationSlug = null,
        protected ?int $serverId = null,
        protected ?int $siteId = null,
        protected array $extra = [],
        protected array $query = [],
    ) {}

    /**
     * Get the resource objects for the current page.
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Get the current page items as a plain array.
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Specify the data which should be serialized to JSON.
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }

    /**
     * Get the cursor for the next page.
     */
    public function nextCursor(): ?string
    {
        return $this->nextCursor;
    }

    /**
     * Determine if there are more pages.
     */
    public function hasMorePages(): bool
    {
        return $this->nextCursor !== null;
    }

    /**
     * Get the number of items per page.
     */
    public function perPage(): ?int
    {
        return $this->perPage;
    }

    /**
     * Fetch and return the next page as a new CursorPaginator.
     */
    public function nextPage(): ?self
    {
        if (! $this->hasMorePages()) {
            return null;
        }

        $query = $this->query;
        $query['page'] = array_merge($query['page'] ?? [], ['cursor' => $this->nextCursor]);

        $response = $this->forge->get($this->uri, $query);

        $data = $response['data'] ?? [];
        $meta = $response['meta'] ?? [];
        $included = $response['included'] ?? [];

        $items = $this->forge->transformCollection(
            $data,
            $this->class,
            $this->organizationSlug,
            $this->serverId,
            $this->siteId,
            $this->extra,
            $included,
        );

        return new self(
            items: $items,
            nextCursor: $meta['next_cursor'] ?? null,
            perPage: $meta['per_page'] ?? null,
            forge: $this->forge,
            uri: $this->uri,
            class: $this->class,
            organizationSlug: $this->organizationSlug,
            serverId: $this->serverId,
            siteId: $this->siteId,
            extra: $this->extra,
            query: $this->query,
        );
    }

    /**
     * Yield all items across all pages by following cursors.
     */
    public function lazy(): Generator
    {
        $page = $this;

        while ($page !== null) {
            yield from $page->items();

            $page = $page->nextPage();
        }
    }

    /**
     * Yield each page as a CursorPaginator.
     */
    public function lazyPages(): Generator
    {
        $page = $this;

        while ($page !== null) {
            yield $page;

            $page = $page->nextPage();
        }
    }

    /**
     * Get an iterator for the current page items.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Get the count of items on the current page.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Determine if the given offset exists.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Get the value at the given offset.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * Set the value at the given offset.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Unset the value at the given offset.
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}
