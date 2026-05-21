<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\NginxTemplate;

class NginxTest extends IntegrationTestCase
{
    public function test_crud_nginx_template(): void
    {
        $org = $this->organization();
        $serverId = $this->serverId();
        $suffix = time();

        // Create
        $template = $this->forge()->createNginxTemplate($org, $serverId, [
            'name' => "SDK Test Template {$suffix}",
            'content' => 'server { listen 80; server_name {{ DOMAIN }}; }',
        ]);

        $this->assertInstanceOf(NginxTemplate::class, $template);
        $this->assertIsInt($template->id);
        $this->assertSame("SDK Test Template {$suffix}", $template->name);

        try {
            usleep(500_000);

            // List
            $templates = $this->forge()->nginxTemplates($org, $serverId);
            $this->assertIsArray($templates);
            $this->assertNotEmpty($templates);

            $found = array_filter($templates, fn (NginxTemplate $t) => $t->id === $template->id);
            $this->assertNotEmpty($found, 'Created template should appear in listing');

            usleep(500_000);

            // Read
            $fetched = $this->forge()->nginxTemplate($org, $serverId, $template->id);
            $this->assertInstanceOf(NginxTemplate::class, $fetched);
            $this->assertSame($template->id, $fetched->id);

            // Properties
            $this->assertIsString($fetched->name);
            $this->assertTrue(
                is_null($fetched->content) || is_string($fetched->content),
                'content should be null or string'
            );
            $this->assertTrue(
                is_null($fetched->createdAt) || is_string($fetched->createdAt),
                'createdAt should be null or string'
            );

            // Envelope keys stripped
            $this->assertIsArray($fetched->relationships);
            $this->assertIsArray($fetched->links);
        } finally {
            usleep(500_000);
            $this->forge()->deleteNginxTemplate($org, $serverId, $template->id);
        }
    }
}
