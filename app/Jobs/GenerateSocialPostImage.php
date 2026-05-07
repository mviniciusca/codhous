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

            // ── Configuration ────────────────────────────────────────
            $manager = new ImageManager(new Driver());
            $preset = $this->post->preset ?? \App\Enums\CardPreset::BOLD_CENTER;
            $style = $preset->getStyle();
            $highlightColor = $this->post->overlay_color ?? '#FFC107';
            $textColor = $this->post->text_color ?? '#ffffff';
            $fontPath = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf'; // Bold base
            
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

            // 2. Render Layout Composition
            if ($style['has_block'] && ($style['block_type'] ?? '') === 'side') {
                // Style: CANVA SIDE
                $image->drawRectangle(function ($draw) use ($highlightColor) {
                    $draw->at(0, 0);
                    $draw->size(486, 1080); // 45% of 1080
                    $draw->background($highlightColor);
                });
                $bg->cover(594, 1080);
                $image->insert($bg, 486, 0, 'top-left');
            } else {
                // Style: Full Image + Overlay
                $image->insert($bg, 0, 0, 'top-left');
                $opacity = $this->post->overlay_opacity ?? 40;
                $image->brightness(-$opacity);
            }

            // 3. Render Text Layers
            $title = $this->post->title;
            $quote = $this->post->quote;
            $align = $this->post->text_align ?? 'center';
            $family = $this->post->font_family ?? 'Inter';
            $isBold = $this->post->is_bold ?? true;
            
            $fontPath = match($family) {
                'Poppins'          => storage_path('app/fonts/Poppins-Bold.ttf'),
                'Montserrat'        => storage_path('app/fonts/Montserrat-ExtraBold.ttf'),
                'Playfair+Display' => storage_path('app/fonts/PlayfairDisplay-BoldItalic.ttf'),
                default            => '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            };

            // Fallback if file doesn't exist
            if (! file_exists($fontPath)) {
                $fontPath = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
            }

            // X & Y position logic from Drag & Drop (converted from 0-100% to 0-1080px)
            $xPos = ($this->post->text_x / 100) * 1080;
            $yPos = ($this->post->text_y / 100) * 1080;

            $finalTextColor = $this->post->text_color ?: '#ffffff';
            if (! str_starts_with($finalTextColor, '#')) {
                $finalTextColor = "#{$finalTextColor}";
            }

            // Render Title (Label)
            if ($title) {
                $image->text($title, $xPos, $yPos - 80, function ($font) use ($fontPath, $finalTextColor, $align) {
                    $font->filename($fontPath);
                    $font->size(25);
                    $font->color($finalTextColor);
                    $font->align($align);
                    $font->valign('bottom');
                });
            }

            // Render Quote (Main Text)
            $image->text($quote, $xPos, $yPos, function ($font) use ($fontPath, $finalTextColor, $align, $style) {
                $font->filename($fontPath);
                $font->size(55);
                $font->color($finalTextColor);
                $font->align($align);
                $font->valign('middle');
                $font->lineHeight(1.2);
                $font->wrap(1000); // Increased wrap width for straighter lines
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
