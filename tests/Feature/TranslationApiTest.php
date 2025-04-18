<?php 

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_create_translation_via_api(): void
    {
        $translationData = Translation::factory()->make()->toArray();

        $this->postJson(route('translations.store'), $translationData)
            ->assertStatus(201)
            ->assertJsonFragment($translationData);
    }

    public function test_can_update_translation_via_api(): void
    {
        $translation = Translation::factory()->create();

        $updateData = [
            'locale' => $translation->locale,
            'key' => $translation->key,
            'content' => 'Updated Text',
        ];

        $this->putJson(route('translations.update', $translation->id), $updateData)
            ->assertStatus(200)
            ->assertJsonFragment(['content' => 'Updated Text']);
    }

    public function test_can_delete_translation_via_api(): void
    {
        $translation = Translation::factory()->create();

        $this->deleteJson(route('translations.destroy', $translation->id))
            ->assertStatus(200);

        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    public function test_can_search_translation_via_api(): void
    {
        Translation::factory()->create(['key' => 'searchable_key', 'content' => 'Searchable Content']);

        $this->getJson(route('translations.search', ['query' => 'searchable_key']))
            ->assertStatus(200)
            ->assertJsonFragment(['key' => 'searchable_key']);
    }
}