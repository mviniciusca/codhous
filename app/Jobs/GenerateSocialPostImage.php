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
                // Style: Full Image + Overlay (FIXED: Real Color Overlay)
                $image->insert($bg, 0, 0, 'top-left');
                
                $color = $this->post->overlay_color ?? '#000000';
                $opacity = $this->post->overlay_opacity ?? 40;

                // Criar uma camada de cor sólida e inserir por cima com opacidade (Jeito estável V3)
                $overlay = $manager->createImage(1080, 1080)->fill($color);
                $image->insert($overlay, 0, 0, 'top-left', $opacity);
                
                // Apply Pattern Layer (Tiled)
                if ($this->post->pattern) {
                    $patternFile = public_path('assets/patterns/' . $this->post->pattern . '.png');
                    if (file_exists($patternFile)) {
                        $pSize = $this->post->pattern_size ?? 10;
                        $renderSize = max(4, $pSize * 2); 
                        
                        $patternTile = $manager->decodePath($patternFile);
                        $patternTile->resize($renderSize, $renderSize);
                        
                        for ($x = 0; $x < 1080; $x += $renderSize) {
                            for ($y = 0; $y < 1080; $y += $renderSize) {
                                $image->insert($patternTile, $x, $y, 'top-left', 0.25); 
                            }
                        }
                    }
                }
            }

            // 3. Render Text Layers
            $title = $this->post->title;
            $quote = $this->post->quote;
            $align = $this->post->text_align ?? 'center';
            $family = $this->post->font_family;
            
            // If custom font is chosen, use the text typed in custom_font
            if ($family === 'custom' && !empty($this->post->custom_font)) {
                $family = $this->post->custom_font;
            }

            // Normalize family name for file
            $familyFile = str_replace([' ', '+'], '', $family);
            $fontPath = storage_path("app/fonts/{$familyFile}.ttf");

            // AUTO-DOWNLOAD FROM GOOGLE FONTS IF MISSING
            if (! file_exists($fontPath)) {
                try {
                    $fontUrlFamily = str_replace(' ', '+', $family);
                    $css = file_get_contents("https://fonts.googleapis.com/css2?family={$fontUrlFamily}:wght@700");
                    if (preg_match('/url\((https:\/\/fonts\.gstatic\.com\/s\/[^\)]+\.ttf)\)/', $css, $matches)) {
                        $ttfUrl = $matches[1];
                        file_put_contents($fontPath, file_get_contents($ttfUrl));
                    }
                } catch (\Exception $e) {
                    $fontPath = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
                }
            }

            // Final check
            if (! file_exists($fontPath)) {
                $fontPath = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
            }

            // X & Y position logic from Drag & Drop (converted from 0-100% to 0-1080px)
            $xPos = ($this->post->text_x / 100) * 1080;
            $yPos = ($this->post->text_y / 100) * 1080;

            // Ajuste de Valign manual baseado no preset (Enum)
            if ($this->post->preset === \App\Enums\CardPreset::TOP) {
                $yPos = 150; // Topo com margem
            } elseif ($this->post->preset === \App\Enums\CardPreset::BOTTOM) {
                $yPos = 930; // Base com margem
            } else {
                $yPos = 540; // Centro
            }

            $finalTextColor = $this->post->text_color ?: '#ffffff';
            if (! str_starts_with($finalTextColor, '#')) {
                $finalTextColor = "#{$finalTextColor}";
            }

            // Render Quote (Main Text)
            $quoteLines = explode("\n", str_replace(["\r\n", "\r"], "\n", $quote));
            $quote = implode("\n", $quoteLines);

            // CORREÇÃO DE ESCALA: O preview é 540px, a saída é 1080px. Logo, fonte x2.
            $finalFontSize = ($this->post->font_size ?? 80) * 2.2;

            $image->text($quote, $xPos, $yPos, function ($font) use ($fontPath, $finalTextColor, $align, $finalFontSize) {
                $font->filename($fontPath);
                $font->size($finalFontSize); 
                $font->color($finalTextColor);
                $font->align($align, 'middle'); 
                $font->lineHeight(1.0);
                $font->wrap(850); 
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
