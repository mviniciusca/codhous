<?php

namespace App\Filament\Resources\MailResource\Pages;

use App\Filament\Resources\BudgetResource;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\MailResource;
use App\Models\Budget;
use App\Models\Customer;
use App\Models\Mail;
use App\Services\SendMailService;
use App\Services\WhatsappService;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

/**
 * Caixa de entrada em dois painéis: lista por pasta à esquerda, leitura e resposta à direita.
 */
class ListMails extends Page
{
    protected static string $resource = MailResource::class;

    protected static string $view = 'filament.mail.inbox';

    protected const PER_PAGE = 30;

    #[Url(as: 'pasta')]
    public string $folder = 'inbox';

    #[Url(as: 'busca')]
    public string $search = '';

    #[Url(as: 'm')]
    public ?int $selected = null;

    public int $limit = self::PER_PAGE;

    /** @var array<string, mixed> */
    public ?array $reply = [];

    public function mount(): void
    {
        if (! array_key_exists($this->folder, MailResource::FOLDERS)) {
            $this->folder = 'inbox';
        }

        if ($this->selected) {
            $this->open($this->selected);
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'Caixa de entrada';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getSubheading(): ?string
    {
        return 'Mensagens recebidas pelo site e e-mails enviados pela equipe.';
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->markAllReadAction(),
            $this->composeAction(),
        ];
    }

    protected function getForms(): array
    {
        return ['replyForm'];
    }

    public function replyForm(Form $form): Form
    {
        return $form
            ->statePath('reply')
            ->schema([
                TextInput::make('subject')
                    ->hiddenLabel()
                    ->prefix('Assunto')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('message')
                    ->hiddenLabel()
                    ->placeholder('Escreva sua resposta…')
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'bulletList', 'orderedList', 'undo', 'redo'])
                    ->required()
                    ->maxLength(5000),
            ]);
    }

    // ------------------------------------------------------------------
    // Navegação
    // ------------------------------------------------------------------

    public function updatedFolder(): void
    {
        if (! array_key_exists($this->folder, MailResource::FOLDERS)) {
            $this->folder = 'inbox';
        }

        $this->limit = self::PER_PAGE;
    }

    public function updatedSearch(): void
    {
        $this->limit = self::PER_PAGE;
    }

    public function loadMore(): void
    {
        $this->limit += self::PER_PAGE;
    }

    /**
     * Abre uma mensagem: marca como lida e prepara a resposta.
     */
    public function open(int $id): void
    {
        $mail = $this->find($id);

        if (! $mail) {
            $this->selected = null;

            return;
        }

        if (! $mail->is_sent && ! $mail->is_read && ! $mail->trashed()) {
            $mail->update(['is_read' => true]);
        }

        $this->selected = $mail->id;
        $this->reply = [];
        $this->replyForm->fill([
            'subject' => Str::startsWith(Str::lower($mail->subject), 're:') ? $mail->subject : 'Re: '.$mail->subject,
            'message' => null,
        ]);
    }

    public function close(): void
    {
        $this->selected = null;
    }

    // ------------------------------------------------------------------
    // Ações rápidas (sem confirmação)
    // ------------------------------------------------------------------

    public function toggleFavorite(int $id): void
    {
        $mail = $this->find($id);
        $mail?->update(['is_favorite' => ! $mail->is_favorite]);
    }

    public function markUnread(int $id): void
    {
        $this->find($id)?->update(['is_read' => false]);
        $this->close();
    }

    public function markSpam(int $id, bool $spam = true): void
    {
        $mail = $this->find($id);
        if (! $mail) {
            return;
        }

        $mail->update(['is_spam' => $spam]);

        Notification::make()
            ->title($spam ? 'Mensagem movida para o spam' : 'Mensagem devolvida à entrada')
            ->success()
            ->send();

        if ($this->folder !== 'trash') {
            $this->close();
        }
    }

    public function restore(int $id): void
    {
        $mail = $this->find($id);
        if (! $mail?->trashed()) {
            return;
        }

        $mail->restore();
        $this->close();

        Notification::make()->title('Mensagem restaurada')->success()->send();
    }

    public function sendReply(): void
    {
        $mail = $this->current;
        if (! $mail || $mail->is_sent) {
            return;
        }

        $data = $this->replyForm->getState();

        $service = (new SendMailService([
            'email' => $mail->email,
            'name' => $mail->name,
            'phone' => $mail->phone,
            'subject' => $data['subject'],
            'message' => $data['message'],
        ]))->send();

        if (! $service->failed()) {
            $this->replyForm->fill(['subject' => $data['subject'], 'message' => null]);
        }
    }

    // ------------------------------------------------------------------
    // Ações com modal
    // ------------------------------------------------------------------

    public function composeAction(): Action
    {
        return Action::make('compose')
            ->label('Nova mensagem')
            ->icon('heroicon-o-pencil-square')
            ->modalHeading('Nova mensagem')
            ->modalDescription('Enviada a partir de '.config('mail.from.address').'.')
            ->modalSubmitActionLabel('Enviar')
            ->modalIcon('heroicon-o-paper-airplane')
            ->closeModalByClickingAway(false)
            ->form([
                TextInput::make('email')
                    ->label('Para')
                    ->email()
                    ->required()
                    ->maxLength(200)
                    ->placeholder('cliente@exemplo.com'),
                TextInput::make('name')
                    ->label('Nome do destinatário')
                    ->maxLength(255)
                    ->placeholder('Opcional'),
                TextInput::make('subject')
                    ->label('Assunto')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('message')
                    ->label('Mensagem')
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'bulletList', 'orderedList', 'undo', 'redo'])
                    ->required()
                    ->maxLength(5000),
            ])
            ->action(function (array $data, Action $action) {
                $data['name'] = filled($data['name'] ?? null) ? $data['name'] : $data['email'];

                if ((new SendMailService($data))->send()->failed()) {
                    $action->halt();
                }
            });
    }

    public function markAllReadAction(): Action
    {
        return Action::make('markAllRead')
            ->label('Marcar todas como lidas')
            ->icon('heroicon-o-check-circle')
            ->color('gray')
            ->visible(fn () => $this->counts['unread'] > 0 && in_array($this->folder, ['inbox', 'unread', 'starred'], true))
            ->requiresConfirmation()
            ->modalHeading('Marcar todas como lidas?')
            ->modalDescription(fn () => $this->counts['unread'].' '.($this->counts['unread'] === 1 ? 'mensagem não lida será marcada' : 'mensagens não lidas serão marcadas').' como lida.')
            ->action(function () {
                MailResource::folderQuery('unread')->update(['is_read' => true]);

                Notification::make()->title('Tudo lido')->success()->send();
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label('Mover para a lixeira')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Mover para a lixeira?')
            ->modalDescription('Você pode restaurar a mensagem pela pasta Lixeira.')
            ->modalSubmitActionLabel('Mover')
            ->action(function (array $arguments) {
                $this->find((int) ($arguments['mail'] ?? 0))?->delete();
                $this->close();

                Notification::make()->title('Mensagem movida para a lixeira')->success()->send();
            });
    }

    public function forceDeleteAction(): Action
    {
        return Action::make('forceDelete')
            ->label('Excluir definitivamente')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Excluir definitivamente?')
            ->modalDescription('Esta ação não pode ser desfeita.')
            ->modalSubmitActionLabel('Excluir')
            ->action(function (array $arguments) {
                $this->find((int) ($arguments['mail'] ?? 0))?->forceDelete();
                $this->close();

                Notification::make()->title('Mensagem excluída')->success()->send();
            });
    }

    // ------------------------------------------------------------------
    // Dados da view
    // ------------------------------------------------------------------

    /** Ações rodam antes do render na mesma requisição: recalcula tudo depois delas */
    public function rendering(): void
    {
        unset($this->counts, $this->mails, $this->current, $this->history, $this->contact);
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function counts(): array
    {
        return collect(array_keys(MailResource::FOLDERS))
            ->mapWithKeys(fn (string $folder) => [$folder => MailResource::folderQuery($folder)->count()])
            ->all();
    }

    #[Computed]
    public function mails(): Collection
    {
        return $this->listQuery()->latest()->limit($this->limit + 1)->get();
    }

    #[Computed]
    public function current(): ?Mail
    {
        return $this->selected ? $this->find($this->selected) : null;
    }

    /**
     * Outras mensagens trocadas com o mesmo e-mail (recebidas e enviadas).
     */
    #[Computed]
    public function history(): Collection
    {
        $mail = $this->current;
        if (! $mail || blank($mail->email)) {
            return collect();
        }

        return Mail::query()
            ->where('email', $mail->email)
            ->whereKeyNot($mail->id)
            ->latest()
            ->limit(5)
            ->get(['id', 'subject', 'is_sent', 'is_spam', 'created_at']);
    }

    /**
     * Cliente cadastrado e orçamentos com o mesmo e-mail.
     *
     * @return array{customer: ?array, budgets: Collection}
     */
    #[Computed]
    public function contact(): array
    {
        $mail = $this->current;
        if (! $mail || blank($mail->email)) {
            return ['customer' => null, 'budgets' => collect()];
        }

        $customer = Customer::query()->where('email', $mail->email)->first(['id', 'name']);

        $budgets = Budget::query()
            ->where('content->customer_email', $mail->email)
            ->latest()
            ->limit(3)
            ->get(['id', 'code', 'status', 'content', 'created_at'])
            ->map(fn (Budget $budget) => [
                'code' => $budget->code,
                'url' => BudgetResource::getUrl('edit', ['record' => $budget]),
                'status' => $budget->status,
                'total' => 'R$ '.number_format(BudgetResource::parseMoney($budget->content['total'] ?? 0), 2, ',', '.'),
            ]);

        return [
            'customer' => $customer ? [
                'name' => $customer->name,
                'url' => CustomerResource::getUrl('edit', ['record' => $customer]),
            ] : null,
            'budgets' => $budgets,
        ];
    }

    public function whatsappUrl(Mail $mail): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $mail->phone);
        if (strlen($digits) < 10) {
            return null;
        }

        $firstName = Str::of($mail->name)->explode(' ')->first();

        return app(WhatsappService::class)->generateUrl($digits, "Olá, {$firstName}! Aqui é da ".config('app.name').', recebemos sua mensagem pelo site.');
    }

    /**
     * Corpo da mensagem: do site chega texto puro; do painel, HTML do editor.
     */
    public function body(Mail $mail): HtmlString
    {
        $message = (string) $mail->message;

        return new HtmlString($message !== strip_tags($message)
            ? Str::sanitizeHtml($message)
            : nl2br(e($message)));
    }

    public function preview(Mail $mail): string
    {
        // Quebra de bloco vira espaço antes de remover as tags ("fim.</p><p>Início" → "fim. Início")
        $text = preg_replace('/<\/(p|div|li|h[1-6])>|<br\s*\/?>/i', ' ', (string) $mail->message);

        return Str::limit(trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($text)))), 140);
    }

    public function shortDate(?Carbon $date): string
    {
        if (! $date) {
            return '';
        }

        $date = $date->timezone(config('app.display_timezone'));

        return match (true) {
            $date->isToday() => $date->format('H:i'),
            $date->isYesterday() => 'Ontem',
            $date->isCurrentYear() => $date->format('d/m'),
            default => $date->format('d/m/y'),
        };
    }

    public function longDate(?Carbon $date): string
    {
        return $date?->timezone(config('app.display_timezone'))->locale('pt_BR')->translatedFormat('j \d\e F \d\e Y, H:i') ?? '';
    }

    public function initials(?string $name): string
    {
        $parts = Str::of((string) $name)->trim()->explode(' ')->filter();

        return Str::upper(Str::substr($parts->first() ?? '?', 0, 1).($parts->count() > 1 ? Str::substr($parts->last(), 0, 1) : ''));
    }

    /**
     * Tom do avatar, estável por e-mail.
     */
    public function avatarTone(?string $seed): int
    {
        return crc32((string) $seed) % 6;
    }

    protected function listQuery(): Builder
    {
        return MailResource::folderQuery($this->folder)
            ->when(filled($this->search), function (Builder $query) {
                $term = '%'.trim($this->search).'%';

                $query->where(fn (Builder $q) => $q
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('subject', 'like', $term)
                    ->orWhere('message', 'like', $term));
            });
    }

    protected function find(int $id): ?Mail
    {
        return Mail::withTrashed()->find($id);
    }
}
