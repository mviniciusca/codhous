<?php

namespace App\Filament\Clusters\NewsletterCluster\Pages;

use App\Filament\Clusters\NewsletterCluster;
use App\Models\Newsletter;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Mail;

class SendNewsletter extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $cluster = NewsletterCluster::class;

    protected static string $view = 'filament.clusters.newsletter-cluster.pages.send-newsletter';

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'Disparo de Mensagens';
    }

    public function getTitle(): string
    {
        return 'Disparo de Mensagens';
    }

    public function getSubheading(): ?string
    {
        return 'Envie e-mails para todos os seus inscritos ativos. Este processo utilizará o SMTP configurado no sistema.';
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('subject')
                    ->label('Assunto')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('body')
                    ->label('Mensagem')
                    ->required(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('send')
                ->label('Enviar Mensagem')
                ->submit('send')
                ->action('sendMessages')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Disparo')
                ->modalDescription('Você tem certeza que deseja enviar esta mensagem para todos os inscritos ativos?'),
        ];
    }

    public function sendMessages(): void
    {
        $data = $this->form->getState();

        $subscribers = Newsletter::where('is_active', true)->get();

        if ($subscribers->isEmpty()) {
            Notification::make()
                ->warning()
                ->title('Nenhum inscrito ativo encontrado.')
                ->send();
            return;
        }

        $count = 0;
        foreach ($subscribers as $subscriber) {
            // Em um ambiente de produção real, usaríamos Jobs/Filas.
            // Para este mini-sistema simples:
            try {
                Mail::html($data['body'], function ($message) use ($subscriber, $data) {
                    $message->to($subscriber->email)
                            ->subject($data['subject']);
                });
                $count++;
            } catch (\Exception $e) {
                // Ignore failure for single subscriber
            }
        }

        Notification::make()
            ->success()
            ->title('Mensagens enviadas com sucesso!')
            ->body("Sua mensagem foi enviada para {$count} inscritos.")
            ->send();

        $this->form->fill();
    }
}
