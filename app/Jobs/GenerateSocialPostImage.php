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

            if (! is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            // ── Generate using Intervention Image v4 ──────────────────
            $manager = new ImageManager(new Driver());
            $preset = $this->post->preset ?? \App\Enums\CardPreset::BOTTOM_RIGHT;
            $highlightColor = $this->post->overlay_color ?? '#FFC107';

            // 1. Load background
            $bgPath = $this->post->backgroundImage?->getFirstMediaPath('image');
            if (! $bgPath || ! file_exists($bgPath)) {
                $bgPath = $this->post->backgroundImage?->getMedia('image')->first()?->getPath();
            }

            if (! $bgPath || ! file_exists($bgPath)) {
                throw new \Exception("Background image not found.");
            }

            // Create Canvas 1080x1080
            $image = $manager->createImage(1080, 1080);
            $bg = $manager->decodePath($bgPath);
            $bg->cover(1080, 1080);

            // 2. Apply Preset Layout Logic
            switch ($preset->value) {
                case \App\Enums\CardPreset::TOP_CENTER->value:
                    // Half color, half image
                    $image->drawRectangle(function ($draw) use ($highlightColor) {
                        $draw->at(0, 0);
                        $draw->size(540, 1080);
                        $draw->background($highlightColor);
                    });
                    $bg->cover(540, 1080);
                    $image->insert($bg, 540, 0, 'top-left');
                    break;

                case \App\Enums\CardPreset::BOTTOM_CENTER->value:
                    // Horizontal Split
                    $bg->cover(1080, 540);
                    $image->insert($bg, 0, 0, 'top-left');
                    $image->drawRectangle(function ($draw) use ($highlightColor) {
                        $draw->at(0, 540);
                        $draw->size(1080, 540);
                        $draw->background($highlightColor);
                    });
                    break;

                case \App\Enums\CardPreset::TOP_LEFT->value:
                    // Thick white border
                    $image->insert($bg, 0, 0, 'top-left');
                    $image->drawRectangle(function ($draw) {
                        $draw->at(40, 40);
                        $draw->size(1000, 1000);
                        $draw->border('white', 20);
                    });
                    // Highlight circle
                    $image->drawCircle(function ($draw) use ($highlightColor) {
                        $draw->at(950, 130);
                        $draw->radius(60);
                        $draw->background($highlightColor);
                    });
                    break;

                default:
                    // Standard: Image + Overlay
                    $image->insert($bg, 0, 0, 'top-left');
                    $opacity = $this->post->overlay_opacity ?? 40;
                    $image->brightness(-$opacity);
            }

            // 4. Add Text (Quote)
            $quote = $this->post->quote ?? '';
            $fontPath = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
            $textColor = $this->post->text_color ?? '#ffffff';
            
            // Adjust text position based on preset
            $xPos = 540;
            $yPos = 540;
            if ($preset->value === \App\Enums\CardPreset::TOP_CENTER->value) $xPos = 270;

            $image->text($quote, $xPos, $yPos, function ($font) use ($fontPath, $textColor) {
                $font->filename($fontPath);
                $font->size(45);
                $font->color($textColor);
                $font->align('center');
                $font->valign('middle');
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
            ]);
            $this->post->update(['status' => 'failed']);
            throw $e;
        }
    }
}
