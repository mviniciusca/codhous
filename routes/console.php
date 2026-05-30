<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('posts:process', function () {
    $posts = \App\Models\SocialPost::whereIn('status', ['queued', 'processing'])->get();

    if ($posts->isEmpty()) {
        return;
    }

    $this->info("Processing " . $posts->count() . " social posts...");

    foreach ($posts as $post) {
        $this->info("Processing post ID: {$post->id}");
        $post->update(['status' => 'processing']);

        try {
            $snapshotFile = storage_path("app/snapshots/{$post->id}.txt");

            if (file_exists($snapshotFile)) {
                $dataUrl = file_get_contents($snapshotFile);
                
                // Run snapshot save synchronously
                $job = new \App\Jobs\SavePostSnapshot($post, $dataUrl);
                $job->handle();

                // Delete temporary file
                @unlink($snapshotFile);
            } else {
                // Run AI generator synchronously
                $job = new \App\Jobs\GenerateSocialPostImage($post);
                $job->handle();
            }
            
            $this->info("Successfully processed post ID: {$post->id}");
        } catch (\Throwable $e) {
            $this->error("Failed to process post ID: {$post->id}. Error: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error("[posts:process] Failed processing post ID: {$post->id}", [
                'error' => $e->getMessage(),
            ]);
            $post->update(['status' => 'failed']);
        }
    }
})->purpose('Process pending social posts via scheduler')->everyMinute();

