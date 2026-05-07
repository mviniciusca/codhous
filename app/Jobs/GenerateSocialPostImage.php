<?php

namespace App\Jobs;

use App\Models\SocialPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class GenerateSocialPostImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly SocialPost $post
    ) {}

    public function handle(): void
    {
        $this->post->update(['status' => 'processing']);

        try {
            $filename = "social-posts/{$this->post->id}-" . now()->format('YmdHis') . '.png';
            $fullPath = storage_path("app/public/{$filename}");

            // Ensure the output directory exists
            if (! is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            // ── Generate using Intervention Image v4 ──────────────────
            $manager = new ImageManager(new Driver());

            // 1. Load background
            $bgPath = $this->post->backgroundImage?->getFirstMediaPath('image');
            
            if (! $bgPath || ! file_exists($bgPath)) {
                $bgPath = $this->post->backgroundImage?->getMedia('image')->first()?->getPath();
            }

            if (! $bgPath || ! file_exists($bgPath)) {
                throw new \Exception("Background image not found.");
            }

            $image = $manager->decodePath($bgPath);

            // 2. Resize to 1080x1080 (square)
            $image->cover(1080, 1080);

            // 3. Apply Overlay
            $opacity = $this->post->overlay_opacity ?? 40;
            $image->brightness(-$opacity);

            // 4. Add Text (Quote)
            $quote = $this->post->quote ?? '';
            $fontPath = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
            $textColor = $this->post->text_color ?? '#ffffff';

            // Draw Quote Marks
            $image->text('"', 540, 400, function ($font) use ($fontPath, $textColor) {
                $font->filename($fontPath);
                $font->size(150);
                $font->color($textColor);
                $font->align('center', 'center'); // horizontal, vertical
            });

            // Draw Text
            $image->text($quote, 540, 540, function ($font) use ($fontPath, $textColor) {
                $font->filename($fontPath);
                $font->size(50);
                $font->color($textColor);
                $font->align('center', 'center');
                $font->lineHeight(1.5);
                $font->wrap(800);
            });

            // 5. Save
            $image->save($fullPath);

            $this->post->update([
                'status'       => 'done',
                'output_path'  => $filename,
                'generated_at' => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error('[GenerateSocialPostImage] Failed', [
                'post_id' => $this->post->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $this->post->update(['status' => 'failed']);

            throw $e;
        }
    }
}
