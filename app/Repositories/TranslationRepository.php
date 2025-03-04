<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function exportTranslations(): StreamedResponse | JsonResponse
    {
        $filePath = 'translations.json';

        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['error' => 'Translations file not found.'], 404);
        }

        return new StreamedResponse(function () use ($filePath) {
            $stream = Storage::disk('local')->readStream($filePath);

            if (!$stream) {
                abort(500, 'Could not open file for reading.');
            }

            while (!feof($stream)) {
                echo fread($stream, 4096);
                flush();
                ob_flush();
            }

            fclose($stream);
        }, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'inline; filename="translations.json"',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

}