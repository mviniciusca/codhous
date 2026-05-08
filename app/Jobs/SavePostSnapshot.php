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
            // Extrair o base64
            if (preg_match('/^data:image\/(\w+);base64,/', $this->dataUrl, $type)) {
                $data = substr($this->dataUrl, strpos($this->dataUrl, ',') + 1);
                $data = base64_decode($data);

                $filename = "social-posts/{$this->post->id}-snapshot-" . now()->format('YmdHis') . '.png';
                $fullPath = storage_path("app/public/{$filename}");

                if (! is_dir(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                file_put_contents($fullPath, $data);

                $this->post->update([
                    'status'       => 'done',
                    'output_path'  => $filename,
                    'generated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[SavePostSnapshot] Failed', [
                'post_id' => $this->post->id,
                'error'   => $e->getMessage(),
            ]);
            $this->post->update(['status' => 'failed']);
        }
    }
}
