<?php 

namespace App\Services;

use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Jobs\ExportTranslationsJob;
use App\Repositories\TranslationRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TranslationService
{
    protected $translationRepository;

    public function __construct(TranslationRepository $translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    public function getAllTranslations(): JsonResponse
    {
        return $this->translationRepository->all();
    }

    public function getTranslationById(int $id): Translation
    {
        return $this->translationRepository->find($id);
    }

    public function createTranslation(array $data): Translation
    {
        $translation = $this->translationRepository->create($data);

        if (isset($data['tags'])) {
            $this->translationRepository->attachTags($translation, $data['tags']);
        }

        ExportTranslationsJob::dispatch();
        return $translation->load('tags');
    }

    public function updateTranslation(Translation $translation, array $data): Translation
    {
        $this->translationRepository->update($translation, $data);

        if (isset($data['tags'])) {
            $this->translationRepository->attachTags($translation, $data['tags']);
        }

        return $translation->load('tags');
    }

    public function deleteTranslation(Translation $translation): bool
    {
        return $this->translationRepository->delete($translation);
    }

    public function searchTranslations(string $query): Collection
    {
        return $this->translationRepository->search($query);
    }

    public function getTranslationsByTag(string $tagName): Collection
    {
        return $this->translationRepository->getTranslationsByTag($tagName);
    }

    public function assignTagsToTranslation(Translation $translation, array $tags): Translation
    {
        return $this->translationRepository->assignTags($translation, $tags);
    }

    public function exportTranslations(): StreamedResponse | JsonResponse
    {
        return $this->translationRepository->exportTranslations();
    }

}