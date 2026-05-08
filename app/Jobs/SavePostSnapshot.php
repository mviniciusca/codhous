<?php

namespace App\Jobs;

use App\Models\SocialPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SavePostSnapshot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly SocialPost $post,
        public readonly string $dataUrl
    ) {}

    public function handle(): void
    {
        try {
            // Use Spatie Media Library to handle the base64 image
            $this->post->addMediaFromBase64($this->dataUrl)
                ->usingFileName("post-{$this->post->id}-" . now()->timestamp . ".png")
                ->toMediaCollection('output');

            $this->post->update([
                'status'       => 'done',
                'generated_at' => now(),
                // 'output_path' is no longer used but kept for migration compatibility if needed
            ]);

        } catch (\Throwable $e) {
            Log::error('[SavePostSnapshot] Failed', [
                'post_id' => $this->post->id,
                'error'   => $e->getMessage(),
            ]);
            $this->post->update(['status' => 'failed']);
        }
    }
}
