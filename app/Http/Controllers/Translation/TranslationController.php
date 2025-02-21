<?php

namespace App\Http\Controllers\Translation;

use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\TranslationService;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;

class TranslationController extends Controller
{

    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->translationService->getAllTranslations());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTranslationRequest $request): JsonResponse
    {
        try {
            $translation = $this->translationService->createTranslation($request->only(['locale', 'key', 'content', 'tags']));
            return response()->json($translation, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json($this->translationService->getTranslationById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTranslationRequest $request, Translation $translation): JsonResponse
    {
        try {
            $data = $request->only(['locale', 'content', 'tags']);
            $updatedTranslation = $this->translationService->updateTranslation($translation, $data);
            return response()->json($updatedTranslation);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Translation $translation): JsonResponse
    {
        try {
            $this->translationService->deleteTranslation($translation);
            return response()->json(['message' => 'Translation deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Search for translations by query.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query');
        $translations = $this->translationService->searchTranslations($query);
        return response()->json($translations);
    }

    
    /**
     * Get all translations that have a given tag name.
     *
     * @param string $tagName
     * @return JsonResponse
     */
    public function getTranslationsByTag(string $tagName): JsonResponse
    {
        $translations = $this->translationService->getTranslationsByTag($tagName);
        return response()->json($translations);
    }

    
    /**
     * Assign tags to a given translation.
     *
     * @param Request $request
     * @param Translation $translation
     * @return JsonResponse
     */
    public function assignTags(Request $request, Translation $translation): JsonResponse
    {
        $tags = $request->get('tags', []);
        $translation = $this->translationService->assignTagsToTranslation($translation, $tags);
        return response()->json($translation);
    }

    
    /**
     * Export all translations as a JSON response.
     *
     * @return JsonResponse
     */
    public function export(): JsonResponse
    {
        $translations = $this->translationService->exportTranslations();
        return response()->json($translations);
    }
}
