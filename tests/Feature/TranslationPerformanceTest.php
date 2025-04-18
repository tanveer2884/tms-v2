<?php

namespace Tests\Feature;

use App\Models\Tag;
use Tests\TestCase;
use App\Models\User;
use App\Models\Translation;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * Test search performance (should return results in under 500ms).
     */
    public function testSearchPerformance()
    {
        $start = microtime(true);

        $response = $this->getJson('/api/translations/search?locale=en&query=test');

        $response->assertStatus(200);

        $executionTime = microtime(true) - $start;
        Log::info("Search API executed in {$executionTime} seconds.");

        $this->assertLessThan(0.5, $executionTime, "Search API took too long!");
    }

    public function testExportPerformance()
    {
        $start = microtime(true);

        $response = $this->getJson('/api/translations/export');

        $response->assertStatus(200);
        $executionTime = microtime(true) - $start;

        Log::info("Export API executed in {$executionTime} seconds.");
        $this->assertLessThan(1, $executionTime, "Export API took too long!");
    }

    public function testAssignTagsPerformance()
    {
        // Create translation and tags if not present
        $translation = Translation::first() ?? Translation::factory()->create();
        $tags = Tag::pluck('id')->take(3);

        // If not enough tags exist, create them
        if ($tags->count() < 3) {
            Tag::factory()->count(3 - $tags->count())->create();
            $tags = Tag::pluck('id')->take(3);
        }

        $start = microtime(true);

        $response = $this->postJson("/api/translations/{$translation->id}/assign-tags", [
            'tag_ids' => $tags->toArray()
        ]);

        $response->assertStatus(200);
        $executionTime = microtime(true) - $start;

        Log::info("Assign Tags API executed in {$executionTime} seconds.");
        $this->assertLessThan(0.5, $executionTime, "Assign Tags API took too long!");
    }
}
