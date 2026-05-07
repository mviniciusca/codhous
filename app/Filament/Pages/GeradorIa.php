<?php

namespace App\Filament\Pages;

use App\Jobs\GenerateSocialPostImage;
use App\Models\BackgroundImage;
use App\Models\SocialPost;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class GeradorIa extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Gerador.IA';

    protected static ?string $title = 'Gerador.IA — Criador de Posts';

    protected static ?string $slug = 'gerador-ia';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.gerador-ia';

    // ─── Form State ───────────────────────────────────────────

    public string $postTitle    = '';
    public string $platform     = 'instagram';
    public string $quote        = '';
    public string $fontFamily   = 'Inter';
    public string $textColor    = '#ffffff';
    public string $overlayColor = '#000000';
    public int    $overlayOpacity = 40;

    /** @var int|null ID of the selected BackgroundImage */
    public ?int $backgroundImageId = null;

    // ─── Computed ─────────────────────────────────────────────

    #[Computed]
    public function backgrounds(): \Illuminate\Database\Eloquent\Collection
    {
        return BackgroundImage::active()->ordered()->get();
    }

    #[Computed]
    public function selectedBackground(): ?BackgroundImage
    {
        return $this->backgroundImageId
            ? BackgroundImage::find($this->backgroundImageId)
            : null;
    }

    #[Computed]
    public function overlayCss(): string
    {
        $hex = ltrim($this->overlayColor, '#');
        [$r, $g, $b] = sscanf($hex, '%02x%02x%02x');
        $alpha = round($this->overlayOpacity / 100, 2);

        return "rgba({$r},{$g},{$b},{$alpha})";
    }

    #[Computed]
    public function fontOptions(): array
    {
        return [
            'Inter'       => 'Inter',
            'Roboto'      => 'Roboto',
            'Montserrat'  => 'Montserrat',
            'Playfair+Display' => 'Playfair Display',
            'Oswald'      => 'Oswald',
            'Lato'        => 'Lato',
            'Poppins'     => 'Poppins',
            'Raleway'     => 'Raleway',
            'Ubuntu'      => 'Ubuntu',
        ];
    }

    #[Computed]
    public function platformOptions(): array
    {
        return [
            'instagram' => ['label' => 'Instagram', 'size' => '1080×1080'],
            'facebook'  => ['label' => 'Facebook',  'size' => '1200×630'],
            'linkedin'  => ['label' => 'LinkedIn',  'size' => '1200×627'],
            'story'     => ['label' => 'Story/Reels', 'size' => '1080×1920'],
        ];
    }

    #[Computed]
    public function recentPosts(): \Illuminate\Database\Eloquent\Collection
    {
        return SocialPost::latest()->take(5)->get();
    }

    // ─── Actions ──────────────────────────────────────────────

    public function selectBackground(int $id): void
    {
        $this->backgroundImageId = ($this->backgroundImageId === $id) ? null : $id;
    }

    public function dispatchGeneration(): void
    {
        $this->validate([
            'postTitle'          => 'required|max:255',
            'quote'              => 'required|max:600',
            'backgroundImageId'  => 'required|exists:background_images,id',
        ], [
            'postTitle.required'         => 'Dê um título para o post.',
            'quote.required'             => 'Digite o texto/quote do post.',
            'backgroundImageId.required' => 'Selecione uma imagem de fundo.',
        ]);

        $post = SocialPost::create([
            'title'               => $this->postTitle,
            'platform'            => $this->platform,
            'quote'               => $this->quote,
            'font_family'         => $this->fontFamily,
            'text_color'          => $this->textColor,
            'overlay_color'       => $this->overlayColor,
            'overlay_opacity'     => $this->overlayOpacity,
            'background_image_id' => $this->backgroundImageId,
            'status'              => 'queued',
        ]);

        GenerateSocialPostImage::dispatch($post);

        Notification::make()
            ->title('Post enviado para a fila!')
            ->body('A imagem será gerada em segundo plano. Você pode continuar trabalhando.')
            ->success()
            ->send();
    }

    public function resetForm(): void
    {
        $this->postTitle        = '';
        $this->quote            = '';
        $this->backgroundImageId = null;
        $this->platform         = 'instagram';
        $this->fontFamily       = 'Inter';
        $this->textColor        = '#ffffff';
        $this->overlayColor     = '#000000';
        $this->overlayOpacity   = 40;
    }

    public function regeneratePost(int $id): void
    {
        $post = SocialPost::find($id);
        
        if ($post) {
            $post->update(['status' => 'queued']);
            GenerateSocialPostImage::dispatch($post);
            
            Notification::make()
                ->title('Post reenviado para a fila!')
                ->success()
                ->send();
        }
    }
}
