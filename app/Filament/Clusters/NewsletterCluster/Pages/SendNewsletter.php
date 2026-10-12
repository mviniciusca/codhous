<?php

namespace App\Filament\Clusters\NewsletterCluster\Pages;

use App\Enums\AccessArea;
use App\Filament\Clusters\NewsletterCluster;
use App\Filament\Concerns\RequiresArea;
use App\Filament\Resources\NewsletterCampaignResource;
use App\Filament\Resources\NewsletterResource;
use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignRecipient;
use App\Services\Newsletter\NewsletterContent;
use App\Services\Newsletter\NewsletterEmail;
use App\Services\Newsletter\NewsletterSender;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use RuntimeException;
use Throwable;

/**
 * Enviar mensagem: editor + prévia do e-mail lado a lado, confirmação com o resumo
 * do envio e acompanhamento do resultado. O envio em si fica em NewsletterSender.
 */
class SendNewsletter extends Page implements HasForms
{
    use InteractsWithForms;
    use RequiresArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Website;
    }

    protected static ?string $cluster = NewsletterCluster::class;

    protected static string $view = 'filament.clusters.newsletter-cluster.pages.send-newsletter';

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    /** 1 = conteúdo, 2 = destinatários (confirmação), 3 = conclusão */
    #[Locked]
    public int $step = 1;

    /** Gerada ao entrar na confirmação; o envio só é criado uma vez por chave */
    #[Locked]
    public ?string $sendKey = null;

    #[Locked]
    public ?int $campaignId = null;

    public bool $confirmDuplicate = false;

    public static function getNavigationLabel(): string
    {
        return 'Enviar mensagem';
    }

    public function getTitle(): string
    {
        return 'Enviar mensagem';
    }

    public function getSubheading(): ?string
    {
        return 'Crie e envie um comunicado por e-mail para todos os inscritos ativos na newsletter, usando o SMTP configurado no sistema.';
    }

    public function getBreadcrumbs(): array
    {
        return [
            NewsletterResource::getUrl('index') => 'Newsletter',
            'Enviar mensagem',
        ];
    }

    /** O workspace usa a largura toda: a navegação do grupo vai pela trilha e pelos atalhos da tela */
    public function getSubNavigation(): array
    {
        return [];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('history')
                ->label('Ver histórico de envios')
                ->icon('heroicon-m-clock')
                ->color('gray')
                ->outlined()
                ->url(NewsletterCampaignResource::getUrl('index')),
        ];
    }

    public function mount(): void
    {
        $this->form->fill();

        // Retomar ou acompanhar um envio (link "Continuar envio" do histórico)
        $resume = request()->integer('envio');

        if ($resume && NewsletterCampaign::whereKey($resume)->exists()) {
            $this->campaignId = $resume;
            $this->step = 3;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('subject')
                    ->label('Assunto')
                    ->placeholder('Ex.: Novidades e atualizações da empresa')
                    ->prefixIcon('heroicon-m-pencil')
                    ->helperText('Aparece na caixa de entrada e como título do e-mail. Prefira até 60 caracteres.')
                    ->required()
                    ->maxLength(150)
                    ->live(debounce: 500),

                RichEditor::make('body')
                    ->label('Mensagem')
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'h2', 'h3', 'blockquote', 'codeBlock', 'bulletList', 'orderedList', 'attachFiles', 'undo', 'redo'])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('newsletter')
                    ->fileAttachmentsVisibility('public')
                    ->helperText(fn (?string $state) => new HtmlString(
                        '<span class="nl-helper"><span>Você pode usar formatação, links e imagens na sua mensagem.</span>'
                        .'<span class="nl-helper-count">'.number_format(NewsletterContent::textLength($state), 0, ',', '.').' caracteres</span></span>'
                    ))
                    ->required()
                    ->rules([
                        fn (): Closure => function (string $attribute, $value, Closure $fail) {
                            if (NewsletterContent::isBlank($value)) {
                                $fail('Escreva a mensagem antes de continuar.');
                            } elseif (NewsletterContent::textLength($value) > NewsletterContent::MAX_TEXT_LENGTH) {
                                $fail('A mensagem passou de '.number_format(NewsletterContent::MAX_TEXT_LENGTH, 0, ',', '.').' caracteres. Resuma o texto.');
                            } elseif (strlen((string) $value) > NewsletterContent::MAX_HTML_BYTES) {
                                $fail('A mensagem ficou grande demais para um e-mail. Remova parte do conteúdo.');
                            }
                        },
                    ])
                    ->live(debounce: 800),

                FileUpload::make('banner')
                    ->label('Imagem de topo (opcional)')
                    ->helperText('Recomendado: 1200 × 400 px, JPG ou PNG de até 2 MB. Exibida no topo do e-mail.')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                    ->maxSize(2048)
                    ->disk('public')
                    ->directory('newsletter')
                    ->visibility('public')
                    ->imagePreviewHeight('120')
                    ->live(),
            ])
            ->statePath('data');
    }

    // ---------- Dados da tela ----------

    public function getActiveSubscribersCount(): int
    {
        return NewsletterSender::activeSubscribers()->count();
    }

    /** @return Collection<int, Newsletter> */
    public function getRecipientSample(): Collection
    {
        return NewsletterSender::activeSubscribers()->latest('id')->limit(4)->get(['id', 'name', 'email']);
    }

    public function getPreviewHtml(): string
    {
        try {
            return NewsletterEmail::render(
                subject: filled($this->data['subject'] ?? null) ? (string) $this->data['subject'] : 'Assunto do e-mail',
                body: NewsletterContent::isBlank($this->data['body'] ?? null)
                    ? '<p style="color:#9ca3af">Comece a escrever a mensagem para ver a prévia aqui.</p>'
                    : (string) $this->data['body'],
                bannerPath: $this->data['banner'] ?? null,
            );
        } catch (Throwable $e) {
            report($e);

            return '<p style="font-family:sans-serif;color:#b91c1c;padding:24px">Não foi possível montar a prévia. Confira o conteúdo da mensagem.</p>';
        }
    }

    public function getDuplicate(): ?NewsletterCampaign
    {
        if (blank($this->data['subject'] ?? null) || NewsletterContent::isBlank($this->data['body'] ?? null)) {
            return null;
        }

        return NewsletterSender::recentDuplicate($this->contentHash());
    }

    public function getCampaign(): ?NewsletterCampaign
    {
        return $this->campaignId ? NewsletterCampaign::find($this->campaignId) : null;
    }

    /** @return Collection<int, NewsletterCampaignRecipient> */
    public function getFailures(): Collection
    {
        return $this->getCampaign()?->recipients()
            ->where('status', NewsletterCampaignRecipient::STATUS_FAILED)
            ->orderBy('id')
            ->limit(50)
            ->get() ?? collect();
    }

    public function getSenderLabel(): string
    {
        $name = config('mail.from.name');
        $address = config('mail.from.address');

        return trim(($name ? "{$name} " : '').($address ? "<{$address}>" : '')) ?: 'Remetente padrão do sistema';
    }

    // ---------- Etapas ----------

    public function review(): void
    {
        $this->form->getState();

        if ($this->getActiveSubscribersCount() === 0) {
            Notification::make()
                ->warning()
                ->title('Nenhum inscrito ativo')
                ->body('Não há quem receba esta mensagem. Ative ou cadastre inscritos antes de enviar.')
                ->send();

            return;
        }

        $this->sendKey ??= (string) Str::uuid();
        $this->confirmDuplicate = false;
        $this->step = 2;
    }

    public function backToEdit(): void
    {
        if ($this->step === 2) {
            $this->step = 1;
        }
    }

    public function send(NewsletterSender $sender): void
    {
        if ($this->step !== 2 || ! $this->sendKey) {
            return;
        }

        $data = $this->form->getState();

        if ($this->getDuplicate() && ! $this->confirmDuplicate) {
            throw ValidationException::withMessages([
                'confirmDuplicate' => 'Confirme que deseja enviar novamente uma mensagem idêntica.',
            ]);
        }

        try {
            $campaign = $sender->create([
                'subject' => $data['subject'],
                'body' => $data['body'],
                'banner' => $this->bannerPath($data['banner'] ?? null),
            ], $this->sendKey, auth()->user());
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title('Envio não iniciado')->body($e->getMessage())->send();

            return;
        }

        $this->campaignId = $campaign->id;
        $this->sendKey = null;
        $this->step = 3;

        // Primeiro lote já nesta requisição; os próximos vêm pelo poll da tela
        $sender->processBatch($campaign);
    }

    public function processNextBatch(NewsletterSender $sender): void
    {
        if ($this->step !== 3 || ! ($campaign = $this->getCampaign()) || ! $campaign->isSending()) {
            return;
        }

        $campaign = $sender->processBatch($campaign);

        if (! $campaign->isSending()) {
            $this->notifyFinished($campaign);
        }
    }

    public function retryFailed(NewsletterSender $sender): void
    {
        if ($campaign = $this->getCampaign()) {
            $sender->retryFailed($campaign);
        }
    }

    public function startNew(): void
    {
        $this->reset(['step', 'sendKey', 'campaignId', 'confirmDuplicate']);
        $this->form->fill();
    }

    // ---------- Apoio ----------

    private function contentHash(): string
    {
        return NewsletterSender::contentHash(
            (string) ($this->data['subject'] ?? ''),
            (string) ($this->data['body'] ?? ''),
            $this->bannerPath($this->data['banner'] ?? null),
        );
    }

    /** Estado do FileUpload: caminho salvo (string) ou lista com um item */
    private function bannerPath(mixed $state): ?string
    {
        if (is_array($state)) {
            $state = reset($state) ?: null;
        }

        return is_string($state) && $state !== '' ? $state : null;
    }

    private function notifyFinished(NewsletterCampaign $campaign): void
    {
        $notification = Notification::make()->title($campaign->statusLabel());

        match ($campaign->status) {
            NewsletterCampaign::STATUS_SENT => $notification->success()->body("A mensagem foi enviada para {$campaign->sent_count} inscritos."),
            NewsletterCampaign::STATUS_PARTIAL => $notification->warning()->body("{$campaign->sent_count} enviados e {$campaign->failed_count} com falha."),
            default => $notification->danger()->body('Nenhum e-mail foi aceito pelo servidor. Confira as configurações de SMTP.'),
        };

        $notification->send();
    }
}
