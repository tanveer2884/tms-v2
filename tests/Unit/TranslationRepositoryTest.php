<?php

namespace Tests\Unit;

use App\Models\Tag;
use Tests\TestCase;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    protected TranslationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TranslationRepository();
    }

    public function test_can_create_translation(): void
    {
        $data = ['locale' => 'es', 'key' => 'saludo', 'content' => 'Hola'];
        $this->repository->create($data);
        $this->assertDatabaseHas('translations', $data);
    }

    public function test_can_find_translation(): void
    {
        $data = ['locale' => 'es', 'key' => 'saludo', 'content' => 'Hola'];
        $translation = $this->repository->create($data);
        $found = $this->repository->find($translation->id);
        $this->assertEquals($translation->id, $found->id);
    }

    public function test_can_update_translation(): void
    {
        $translation = Translation::factory()->create();
        $this->repository->update($translation, ['content' => 'Updated Text']);
        $this->assertDatabaseHas('translations', ['id' => $translation->id, 'content' => 'Updated Text']);
    }

    public function test_can_delete_translation(): void
    {
        $translation = Translation::factory()->create();
        $this->repository->delete($translation);
        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    public function test_can_search_translations(): void
    {
        Translation::factory()->create(['key' => 'test_key', 'content' => 'Test Content']);
        $results = $this->repository->searchTranslations('test_key');
        $this->assertNotEmpty($results);
    }

    public function test_can_assign_tags(): void
    {
        $translation = Translation::factory()->create();
        $this->repository->assignTags($translation, ['greeting', 'welcome']);
        $this->assertCount(2, $translation->tags);
    }

    public function test_get_translations_by_tag(): void
    {
        $tag = Tag::create(['name' => 'greeting']);;
        $translation = Translation::factory()->create(['key' => 'test_key', 'content' => 'Test Content']);
        $translation->tags()->attach($tag->id);
        $results = $this->repository->getTranslationsByTag('greeting');
        $this->assertNotEmpty($results);
    }

    public function test_get_translations_by_non_existing_tag()
    {
        $translations = $this->repository->getTranslationsByTag('nonexistent');
        $this->assertCount(0, $translations);
    }

    public function test_remove_tags_from_translation(): void
    {
        $tags = ['greeting'];
        $translation = Translation::factory()->create();
        $this->repository->assignTags($translation, $tags);
        $translation->tags()->detach(Tag::where('name', 'morning')->first());
        $this->assertCount(1, $translation->tags);
    }

    public function test_remove_multiple_tags_from_translation_using_detach(): void
    {
        $translation = Translation::factory()->create();
        $tags = ['greeting', 'welcome', 'morning'];
        $this->repository->assignTags($translation, $tags);
        $this->assertCount(3, $translation->tags);
        $tagsToRemove = Tag::whereIn('name', ['greeting', 'welcome'])->pluck('id')->toArray();
        $translation->tags()->detach($tagsToRemove);
        $translation->refresh();
        $this->assertCount(1, $translation->tags);
        $this->assertEquals('morning', $translation->tags->first()->name);
    }
}