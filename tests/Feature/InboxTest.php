<?php

use App\Filament\Resources\MailResource;
use App\Filament\Resources\MailResource\Pages\CreateMail;
use App\Filament\Resources\MailResource\Pages\ListMails;
use App\Filament\Resources\MailResource\Pages\ViewMail;
use App\Mail\Message;
use App\Models\Mail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail as Mailer;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mailer::fake();
    $this->actingAs(User::factory()->create());

    $make = fn (array $attributes) => Mail::create(array_merge([
        'user_id' => null,
        'name' => 'Cliente',
        'email' => 'cliente@example.com',
        'phone' => '(21) 98877-6655',
        'subject' => 'Dúvida',
        'message' => 'Mensagem enviada pelo formulário do site.',
        'is_sent' => false,
        'is_read' => false,
        'is_favorite' => false,
        'is_spam' => false,
    ], $attributes));

    $this->unread = $make(['name' => 'André Castro', 'email' => 'andre@horizonte.com.br', 'subject' => 'Orçamento C30']);
    $this->read = $make(['name' => 'Larissa Pereira', 'is_read' => true, 'is_favorite' => true]);
    $this->sent = $make(['name' => 'João Silva', 'is_sent' => true, 'is_read' => true, 'subject' => 'Re: Elogio']);
    $this->spam = $make(['name' => 'Promo SEO', 'is_spam' => true]);
    $this->trashed = $make(['name' => 'Carlos Pinto']);
    $this->trashed->delete();
});

test('cada pasta mostra só as suas mensagens', function () {
    $expect = [
        'inbox' => ['André Castro', 'Larissa Pereira'],
        'unread' => ['André Castro'],
        'starred' => ['Larissa Pereira'],
        'sent' => ['João Silva'],
        'spam' => ['Promo SEO'],
        'trash' => ['Carlos Pinto'],
    ];

    foreach ($expect as $folder => $names) {
        expect(MailResource::folderQuery($folder)->pluck('name')->sort()->values()->all())->toBe($names);
    }

    expect(MailResource::count())->toBe('1');
});

test('abrir uma mensagem marca como lida e prepara o assunto da resposta', function () {
    Livewire::test(ListMails::class)
        ->call('open', $this->unread->id)
        ->assertSet('selected', $this->unread->id)
        ->assertSet('reply.subject', 'Re: Orçamento C30')
        ->assertSee('andre@horizonte.com.br')
        ->assertSee('Chamar no WhatsApp');

    expect($this->unread->fresh()->is_read)->toBeTrue();
});

test('busca filtra a lista por nome, e-mail ou assunto', function () {
    Livewire::test(ListMails::class)
        ->set('search', 'horizonte')
        ->assertSee('André Castro')
        ->assertDontSee('Larissa Pereira');
});

test('estrela, não lida e spam atualizam a mensagem', function () {
    $page = Livewire::test(ListMails::class)->call('open', $this->unread->id);

    $page->call('toggleFavorite', $this->unread->id);
    expect($this->unread->fresh()->is_favorite)->toBeTrue();

    $page->call('markUnread', $this->unread->id)->assertSet('selected', null);
    expect($this->unread->fresh()->is_read)->toBeFalse();

    $page->call('markSpam', $this->unread->id);
    expect($this->unread->fresh()->is_spam)->toBeTrue();
});

test('lixeira: mover, restaurar e excluir definitivamente', function () {
    $page = Livewire::test(ListMails::class);

    $page->callAction('delete', arguments: ['mail' => $this->read->id]);
    expect($this->read->fresh()->trashed())->toBeTrue();

    $page->call('restore', $this->read->id);
    expect($this->read->fresh()->trashed())->toBeFalse();

    $page->callAction('forceDelete', arguments: ['mail' => $this->trashed->id]);
    expect(Mail::withTrashed()->find($this->trashed->id))->toBeNull();
});

test('resposta é enviada ao cliente e registrada em enviadas com o destinatário', function () {
    Livewire::test(ListMails::class)
        ->call('open', $this->unread->id)
        ->set('reply.message', '<p>Olá, André! Temos disponibilidade.</p>')
        ->call('sendReply')
        ->assertHasNoErrors()
        ->assertSet('reply.message', null);

    Mailer::assertSent(Message::class, fn (Message $mail) => $mail->hasTo('andre@horizonte.com.br'));

    $sent = Mail::where('is_sent', true)->latest('id')->first();
    expect($sent->email)->toBe('andre@horizonte.com.br')
        ->and($sent->name)->toBe('André Castro')
        ->and($sent->subject)->toBe('Re: Orçamento C30');
});

test('resposta exige mensagem', function () {
    Livewire::test(ListMails::class)
        ->call('open', $this->unread->id)
        ->call('sendReply')
        ->assertHasErrors(['reply.message']);

    Mailer::assertNothingSent();
});

test('nova mensagem envia e registra em enviadas', function () {
    Livewire::test(ListMails::class)
        ->callAction('compose', data: [
            'email' => 'novo@cliente.com',
            'subject' => 'Tabela de preços',
            'message' => '<p>Segue a tabela.</p>',
        ])
        ->assertHasNoActionErrors();

    Mailer::assertSent(Message::class, fn (Message $mail) => $mail->hasTo('novo@cliente.com'));
    expect(Mail::where('is_sent', true)->where('email', 'novo@cliente.com')->exists())->toBeTrue();
});

test('histórico mostra outras mensagens do mesmo e-mail', function () {
    Mail::create(['name' => 'André Castro', 'email' => 'andre@horizonte.com.br', 'subject' => 'Re: Orçamento C30', 'message' => 'Resposta', 'is_sent' => true]);

    Livewire::test(ListMails::class)
        ->call('open', $this->unread->id)
        ->assertSee('Outras mensagens')
        ->assertSee('Re: Orçamento C30');
});

test('rotas antigas de visualização e criação levam à caixa', function () {
    Livewire::test(ViewMail::class, ['record' => $this->unread->id])
        ->assertRedirect(MailResource::getUrl('index', ['pasta' => 'inbox', 'm' => $this->unread->id]));

    Livewire::test(CreateMail::class)
        ->assertRedirect(MailResource::getUrl('index', ['action' => 'compose']));
});
