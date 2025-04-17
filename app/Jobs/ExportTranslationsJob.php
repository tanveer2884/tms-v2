<?php

namespace App\Jobs;

use App\Models\Translation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportTranslationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filePath = 'translations.json';

        $tempFile = tempnam(sys_get_temp_dir(), 'translations_');
        $stream = fopen($tempFile, 'w');

        if (!$stream) {
            Log::error('ExportTranslationsJob: Failed to create temporary file.');
            return;
        }

        fwrite($stream, '[');

        $firstChunk = true;

        Translation::with('tags')->chunk(5000, function ($translations) use ($stream, &$firstChunk) {
            $jsonData = $translations->toJson(JSON_PRETTY_PRINT);

            $jsonData = trim($jsonData, "[]");

            if (!$firstChunk) {
                fwrite($stream, ",");
            }

            fwrite($stream, $jsonData);
            $firstChunk = false;
        });

        fwrite($stream, ']');

        fclose($stream);

        Storage::disk('local')->put($filePath, file_get_contents($tempFile));

        // Delete the temporary file
        unlink($tempFile);

        Log::info('ExportTranslationsJob: JSON file successfully exported.');
    }
}
