<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Js;

class TranslationRepository
{
    public function all(): JsonResponse
    {
        $data = DB::table('translations')
        ->select('id', 'locale', 'key', 'content', 'created_at', 'updated_at')
        ->get();

        return response()->json($data);
    }

    public function find(int $id): Translation
    {
        return Cache::remember("translations.{$id}", 60, function () use ($id) {
            return Translation::with('tags')->findOrFail($id);
        });
    }

    public function create(array $data): Translation
    {
        Cache::forget('translations.all');
        return Translation::create($data);
    }


    public function update(Translation $translation, array $data): Translation
    {
        $translation->update($data);
        Cache::forget('translations.all');
        Cache::forget("translations.{$translation->id}");
        return $translation;
    }

    public function delete(Translation $translation): bool
    {
        return $translation->delete();
    }

    public function attachTags(Translation $translation, array $tags): Translation
    {
        $tagIds = collect($tags)->map(function (string $tagName): int {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        })->toArray();

        $translation->tags()->sync($tagIds);

        Cache::forget('translations.all');
        Cache::forget("translations.{$translation->id}");

        return $translation;
    }

    public function search(string $query): Collection
    {
        return Translation::search($query)->get();
    }

    public function getTranslationsByTag(string $tagName): Collection
    {
        return Translation::whereHas('tags', function ($query) use ($tagName) {
            $query->where('name', $tagName);
        })->with('tags')->get();
    }

    public function assignTags(Translation $translation, array $tags): Translation
    {
        $tagIds = collect($tags)->map(function (string $tagName): int {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        })->toArray();

        $translation->tags()->attach($tagIds);

        Cache::forget('translations.all');
        Cache::forget("translations.{$translation->id}");

        return $translation->load('tags');
    }

    public function exportTranslations(): JsonResponse
    {
        $data = DB::table('translations')
        ->leftJoin('translation_tag', 'translations.id', '=', 'translation_tag.translation_id')
        ->leftJoin('tags', 'translation_tag.tag_id', '=', 'tags.id')
        ->select(
            'translations.id as translation_id',
            'translations.locale',
            'translations.key',
            'translations.content',
            DB::raw('GROUP_CONCAT(tags.name) as tag_names') // Concatenate tag names
        )
            ->groupBy('translations.id')
            ->get();

        return response()->json($data);
    }

}