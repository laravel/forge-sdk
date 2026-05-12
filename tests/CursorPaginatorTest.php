<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Server;
use Mockery;
use PHPUnit\Framework\TestCase;

class CursorPaginatorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    protected function makePaginator(
        array $items = [],
        ?string $nextCursor = null,
        ?int $perPage = null,
        ?Forge $forge = null,
        string $uri = 'orgs/org-123/servers',
        string $class = Server::class,
        ?string $organizationSlug = 'org-123',
    ): CursorPaginator {
        $forge ??= new Forge('123', Mockery::mock(Client::class));

        return new CursorPaginator(
            items: $items,
            nextCursor: $nextCursor,
            perPage: $perPage,
            forge: $forge,
            uri: $uri,
            class: $class,
            organizationSlug: $organizationSlug,
        );
    }

    public function test_items_returns_resource_objects()
    {
        $server = new Server(['id' => 1, 'name' => 'Server 1']);
        $paginator = $this->makePaginator(items: [$server]);

        $this->assertSame([$server], $paginator->items());
    }

    public function test_next_cursor_returns_cursor_string()
    {
        $paginator = $this->makePaginator(nextCursor: 'abc123');

        $this->assertSame('abc123', $paginator->nextCursor());
    }

    public function test_next_cursor_returns_null_when_no_more_pages()
    {
        $paginator = $this->makePaginator(nextCursor: null);

        $this->assertNull($paginator->nextCursor());
    }

    public function test_has_more_pages_returns_true_when_cursor_exists()
    {
        $paginator = $this->makePaginator(nextCursor: 'abc123');

        $this->assertTrue($paginator->hasMorePages());
    }

    public function test_has_more_pages_returns_false_when_no_cursor()
    {
        $paginator = $this->makePaginator(nextCursor: null);

        $this->assertFalse($paginator->hasMorePages());
    }

    public function test_per_page_returns_value()
    {
        $paginator = $this->makePaginator(perPage: 15);

        $this->assertSame(15, $paginator->perPage());
    }

    public function test_per_page_returns_null_when_not_set()
    {
        $paginator = $this->makePaginator(perPage: null);

        $this->assertNull($paginator->perPage());
    }

    public function test_count_returns_current_page_item_count()
    {
        $items = [
            new Server(['id' => 1, 'name' => 'Server 1']),
            new Server(['id' => 2, 'name' => 'Server 2']),
            new Server(['id' => 3, 'name' => 'Server 3']),
        ];
        $paginator = $this->makePaginator(items: $items);

        $this->assertCount(3, $paginator);
        $this->assertSame(3, count($paginator));
    }

    public function test_count_returns_zero_for_empty_page()
    {
        $paginator = $this->makePaginator(items: []);

        $this->assertCount(0, $paginator);
    }

    public function test_foreach_iterates_current_page_items()
    {
        $items = [
            new Server(['id' => 1, 'name' => 'Server 1']),
            new Server(['id' => 2, 'name' => 'Server 2']),
        ];
        $paginator = $this->makePaginator(items: $items);

        $iterated = [];
        foreach ($paginator as $item) {
            $iterated[] = $item;
        }

        $this->assertSame($items, $iterated);
    }

    public function test_array_access_by_index()
    {
        $server = new Server(['id' => 1, 'name' => 'Server 1']);
        $paginator = $this->makePaginator(items: [$server]);

        $this->assertSame($server, $paginator[0]);
    }

    public function test_array_access_offset_exists()
    {
        $paginator = $this->makePaginator(items: [
            new Server(['id' => 1, 'name' => 'Server 1']),
        ]);

        $this->assertTrue(isset($paginator[0]));
        $this->assertFalse(isset($paginator[1]));
    }

    public function test_array_access_offset_set()
    {
        $paginator = $this->makePaginator(items: []);
        $server = new Server(['id' => 1, 'name' => 'Server 1']);

        $paginator[0] = $server;

        $this->assertSame($server, $paginator[0]);
    }

    public function test_array_access_offset_unset()
    {
        $paginator = $this->makePaginator(items: [
            new Server(['id' => 1, 'name' => 'Server 1']),
        ]);

        unset($paginator[0]);

        $this->assertFalse(isset($paginator[0]));
    }

    public function test_next_page_returns_null_when_no_more_pages()
    {
        $paginator = $this->makePaginator(nextCursor: null);

        $this->assertNull($paginator->nextPage());
    }

    public function test_next_page_fetches_next_page()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', ['query' => ['cursor' => 'cursor-page-2']])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 3, 'name' => 'Server 3'],
                    ['id' => 4, 'name' => 'Server 4'],
                ],
                'meta' => [
                    'next_cursor' => 'cursor-page-3',
                    'per_page' => 2,
                ],
            ]))
        );

        $paginator = new CursorPaginator(
            items: [
                new Server(['id' => 1, 'name' => 'Server 1'], $forge),
                new Server(['id' => 2, 'name' => 'Server 2'], $forge),
            ],
            nextCursor: 'cursor-page-2',
            perPage: 2,
            forge: $forge,
            uri: 'orgs/org-123/servers',
            class: Server::class,
            organizationSlug: 'org-123',
        );

        $nextPage = $paginator->nextPage();

        $this->assertInstanceOf(CursorPaginator::class, $nextPage);
        $this->assertCount(2, $nextPage);
        $this->assertInstanceOf(Server::class, $nextPage[0]);
        $this->assertSame(3, $nextPage[0]->id);
        $this->assertSame('cursor-page-3', $nextPage->nextCursor());
        $this->assertTrue($nextPage->hasMorePages());
    }

    public function test_next_page_returns_paginator_with_no_cursor_on_last_page()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', ['query' => ['cursor' => 'cursor-last']])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 5, 'name' => 'Server 5'],
                ],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 2,
                ],
            ]))
        );

        $paginator = new CursorPaginator(
            items: [new Server(['id' => 4, 'name' => 'Server 4'], $forge)],
            nextCursor: 'cursor-last',
            perPage: 2,
            forge: $forge,
            uri: 'orgs/org-123/servers',
            class: Server::class,
            organizationSlug: 'org-123',
        );

        $nextPage = $paginator->nextPage();

        $this->assertInstanceOf(CursorPaginator::class, $nextPage);
        $this->assertCount(1, $nextPage);
        $this->assertNull($nextPage->nextCursor());
        $this->assertFalse($nextPage->hasMorePages());
    }

    public function test_lazy_yields_all_items_across_pages()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', ['query' => ['cursor' => 'cursor-2']])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 3, 'name' => 'Server 3'],
                ],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 2,
                ],
            ]))
        );

        $paginator = new CursorPaginator(
            items: [
                new Server(['id' => 1, 'name' => 'Server 1'], $forge),
                new Server(['id' => 2, 'name' => 'Server 2'], $forge),
            ],
            nextCursor: 'cursor-2',
            perPage: 2,
            forge: $forge,
            uri: 'orgs/org-123/servers',
            class: Server::class,
            organizationSlug: 'org-123',
        );

        $allItems = iterator_to_array($paginator->lazy(), false);

        $this->assertCount(3, $allItems);
        $this->assertSame(1, $allItems[0]->id);
        $this->assertSame(2, $allItems[1]->id);
        $this->assertSame(3, $allItems[2]->id);
    }

    public function test_lazy_yields_only_current_page_when_no_more_pages()
    {
        $paginator = $this->makePaginator(
            items: [new Server(['id' => 1, 'name' => 'Server 1'])],
            nextCursor: null,
        );

        $allItems = iterator_to_array($paginator->lazy(), false);

        $this->assertCount(1, $allItems);
    }

    public function test_lazy_pages_yields_each_page_as_paginator()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', ['query' => ['cursor' => 'cursor-2']])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 3, 'name' => 'Server 3'],
                ],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 2,
                ],
            ]))
        );

        $paginator = new CursorPaginator(
            items: [
                new Server(['id' => 1, 'name' => 'Server 1'], $forge),
                new Server(['id' => 2, 'name' => 'Server 2'], $forge),
            ],
            nextCursor: 'cursor-2',
            perPage: 2,
            forge: $forge,
            uri: 'orgs/org-123/servers',
            class: Server::class,
            organizationSlug: 'org-123',
        );

        $pages = iterator_to_array($paginator->lazyPages(), false);

        $this->assertCount(2, $pages);
        $this->assertInstanceOf(CursorPaginator::class, $pages[0]);
        $this->assertInstanceOf(CursorPaginator::class, $pages[1]);
        $this->assertCount(2, $pages[0]);
        $this->assertCount(1, $pages[1]);
    }

    public function test_next_page_preserves_context_args()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', ['query' => ['cursor' => 'next']])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 10, 'name' => 'Server 10'],
                ],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 10,
                ],
            ]))
        );

        $paginator = new CursorPaginator(
            items: [],
            nextCursor: 'next',
            perPage: 10,
            forge: $forge,
            uri: 'orgs/org-123/servers',
            class: Server::class,
            organizationSlug: 'org-123',
            serverId: 1,
        );

        $nextPage = $paginator->nextPage();

        $this->assertInstanceOf(CursorPaginator::class, $nextPage);
        $this->assertCount(1, $nextPage);
        // Verify that the organization context was preserved — the resource should have organization_id injected
        $this->assertSame('org-123', $nextPage[0]->organizationId);
    }

    public function test_next_page_preserves_original_query_params()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [
            'query' => ['per_page' => 50, 'filter' => 'active', 'cursor' => 'cursor-2'],
        ])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 3, 'name' => 'Server 3'],
                ],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 50,
                ],
            ]))
        );

        $paginator = new CursorPaginator(
            items: [
                new Server(['id' => 1, 'name' => 'Server 1'], $forge),
                new Server(['id' => 2, 'name' => 'Server 2'], $forge),
            ],
            nextCursor: 'cursor-2',
            perPage: 50,
            forge: $forge,
            uri: 'orgs/org-123/servers',
            class: Server::class,
            organizationSlug: 'org-123',
            query: ['per_page' => 50, 'filter' => 'active'],
        );

        $nextPage = $paginator->nextPage();

        $this->assertInstanceOf(CursorPaginator::class, $nextPage);
        $this->assertCount(1, $nextPage);
        $this->assertNull($nextPage->nextCursor());
    }

    public function test_backward_compatibility_foreach_count_and_index_access()
    {
        $items = [
            new Server(['id' => 1, 'name' => 'Server 1']),
            new Server(['id' => 2, 'name' => 'Server 2']),
            new Server(['id' => 3, 'name' => 'Server 3']),
        ];
        $paginator = $this->makePaginator(items: $items);

        // foreach works
        $foreachCount = 0;
        foreach ($paginator as $item) {
            $this->assertInstanceOf(Server::class, $item);
            $foreachCount++;
        }
        $this->assertSame(3, $foreachCount);

        // count() works
        $this->assertSame(3, count($paginator));

        // $result[0] works
        $this->assertSame($items[0], $paginator[0]);
        $this->assertSame($items[2], $paginator[2]);
    }
}
