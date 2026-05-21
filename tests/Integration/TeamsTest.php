<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Team;
use Laravel\Forge\Resources\TeamMember;

class TeamsTest extends IntegrationTestCase
{
    public function test_list_teams(): void
    {
        $teams = $this->forge()->teams($this->organization());

        $this->assertInstanceOf(CursorPaginator::class, $teams);

        if (count($teams) > 0) {
            $this->assertContainsOnlyInstancesOf(Team::class, $teams);

            $team = $teams[0];
            $this->assertInstanceOf(Team::class, $team);
            $this->assertIsInt($team->id);
            $this->assertIsString($team->name);
            $this->assertNotEmpty($team->name);

            // Envelope keys stripped
            $this->assertIsArray($team->relationships);
            $this->assertIsArray($team->links);
        }
    }

    public function test_crud_team(): void
    {
        $org = $this->organization();
        $suffix = time();

        // Create
        $team = $this->forge()->createTeam($org, [
            'name' => "SDK Test Team {$suffix}",
        ]);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertIsInt($team->id);
        $this->assertSame("SDK Test Team {$suffix}", $team->name);

        try {
            usleep(500_000);

            // Read
            $fetched = $this->forge()->team($org, $team->id);
            $this->assertSame($team->id, $fetched->id);

            usleep(500_000);

            // Update
            $updated = $this->forge()->updateTeam($org, $team->id, [
                'name' => "SDK Test Team {$suffix} Updated",
            ]);

            $this->assertSame("SDK Test Team {$suffix} Updated", $updated->name);

            usleep(500_000);

            // List members (should include at least the creator)
            $members = $this->forge()->teamMembers($org, $team->id);
            $this->assertInstanceOf(CursorPaginator::class, $members);

            if (count($members) > 0) {
                $this->assertContainsOnlyInstancesOf(TeamMember::class, $members);

                $member = $members[0];
                $this->assertInstanceOf(TeamMember::class, $member);
                $this->assertIsInt($member->id);
                $this->assertIsString($member->name);
                $this->assertIsString($member->email);

                $this->assertTrue(
                    is_null($member->updatedAt) || is_string($member->updatedAt),
                    'updatedAt should be null or string'
                );
            }
        } finally {
            usleep(500_000);

            // Delete (always clean up)
            $this->forge()->deleteTeam($org, $team->id);
        }
    }
}
