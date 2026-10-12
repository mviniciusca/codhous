<?php

use App\Filament\Clusters\NewsletterCluster\Pages\SendNewsletter;
use App\Filament\Resources\NewsletterCampaignResource\Pages\ListNewsletterCampaigns;
use App\Filament\Resources\NewsletterCampaignResource\Pages\ViewNewsletterCampaign;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignRecipient;
use App\Models\Setting;
use App\Models\User;
use App\Services\Newsletter\NewsletterContent;
use App\Services\Newsletter\NewsletterSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['settings' => ['maintenance_mode' => false]]);

    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Newsletter::create(['name' => 'Ana Souza', 'email' => 'ana@example.com', 'is_active' => true]);
    Newsletter::create(['name' => 'João Lima', 'email' => 'joao@example.com', 'is_active' => true]);
    Newsletter::create(['name' => 'Inativo', 'email' => 'inativo@example.com', 'is_active' => false]);
});

function fillMessage($component, string $subject = 'Novidades da Codhous', string $body = '<div>Olá, temos <strong>novidades</strong>!</div>')
{
    return $component->fillForm(['subject' => $subject, 'body' => $body]);
}

test('tela de envio mostra editor, prévia, etapas e barra de destinatários', function () {
    $this->get(SendNewsletter::getUrl())
        ->assertOk()
        ->assertSee('cx-pagebar', escape: false)
        ->assertSee('Conteúdo da mensagem')
        ->assertSee('Pré-visualização do e-mail')
        ->assertSee('Revisar e confirmar envio')
        ->assertSee('Ver histórico de envios')
        ->assertSee('Ver inscritos');

    livewire(SendNewsletter::class)
        ->assertOk()
        ->assertSee('inscritos ativos')
        ->assertSeeHtml('class="nl-bar-value">2</span>');
});

test('prévia reflete o assunto e a mensagem editados', function () {
    $component = fillMessage(livewire(SendNewsletter::class), 'Promoção de outubro', '<div>Concreto com desconto</div>');

    expect($component->instance()->getPreviewHtml())
        ->toContain('Promoção de outubro')
        ->toContain('Concreto com desconto');
});

test('não avança para a confirmação sem assunto e mensagem', function () {
    livewire(SendNewsletter::class)
        ->fillForm(['subject' => '', 'body' => '<div><br></div>'])
        ->call('review')
        ->assertHasFormErrors(['subject' => 'required', 'body'])
        ->assertSet('step', 1);
});

test('confirmação mostra assunto, conteúdo e quantidade de destinatários', function () {
    fillMessage(livewire(SendNewsletter::class))
        ->call('review')
        ->assertHasNoFormErrors()
        ->assertSet('step', 2)
        ->assertSee('Revise antes de enviar')
        ->assertSee('Novidades da Codhous')
        ->assertSee('2 inscritos ativos')
        ->assertSee('Confirmar e enviar para 2');
});

test('envia só para inscritos ativos e registra o histórico', function () {
    Mail::fake();

    fillMessage(livewire(SendNewsletter::class))
        ->call('review')
        ->call('send')
        ->assertSet('step', 3)
        ->assertSee('Mensagem enviada');

    Mail::assertSent(NewsletterMail::class, 2);
    Mail::assertSent(NewsletterMail::class, fn (NewsletterMail $mail) => $mail->hasTo('ana@example.com'));
    Mail::assertNotSent(NewsletterMail::class, fn (NewsletterMail $mail) => $mail->hasTo('inativo@example.com'));

    $campaign = NewsletterCampaign::sole();

    expect($campaign->status)->toBe(NewsletterCampaign::STATUS_SENT)
        ->and($campaign->recipients_count)->toBe(2)
        ->and($campaign->sent_count)->toBe(2)
        ->and($campaign->failed_count)->toBe(0)
        ->and($campaign->user_id)->toBe($this->user->id)
        ->and($campaign->finished_at)->not->toBeNull();
});

test('clique repetido em enviar não cria outro envio', function () {
    Mail::fake();

    $component = fillMessage(livewire(SendNewsletter::class))->call('review');
    $key = $component->get('sendKey');

    $component->call('send')->call('send');

    // Mesma chave (requisições simultâneas) devolve o envio existente
    app(NewsletterSender::class)->create(['subject' => 'Novidades da Codhous', 'body' => '<div>Olá</div>'], $key);

    expect(NewsletterCampaign::count())->toBe(1);
    Mail::assertSent(NewsletterMail::class, 2);
});

test('mensagem idêntica enviada há pouco exige confirmação extra', function () {
    Mail::fake();

    fillMessage(livewire(SendNewsletter::class))->call('review')->call('send');

    fillMessage(livewire(SendNewsletter::class))
        ->call('review')
        ->assertSee('Esta mesma mensagem já foi enviada')
        ->call('send')
        ->assertHasErrors('confirmDuplicate')
        ->assertSet('step', 2)
        ->set('confirmDuplicate', true)
        ->call('send')
        ->assertSet('step', 3);

    expect(NewsletterCampaign::count())->toBe(2);
});

test('falhas do servidor ficam registradas e podem ser reenviadas', function () {
    Mail::shouldReceive('to')->andReturnUsing(function ($email) {
        $pending = Mockery::mock();
        $pending->shouldReceive('send')->andReturnUsing(function () use ($email) {
            if ($email === 'joao@example.com') {
                throw new RuntimeException('550 Mailbox unavailable');
            }
        });

        return $pending;
    });

    fillMessage(livewire(SendNewsletter::class))
        ->call('review')
        ->call('send')
        ->assertSee('Mensagem enviada com falhas')
        ->assertSee('joao@example.com')
        ->assertSee('O servidor recusou o destinatário');

    $campaign = NewsletterCampaign::sole();
    $failure = $campaign->recipients()->where('status', NewsletterCampaignRecipient::STATUS_FAILED)->sole();

    expect($campaign->status)->toBe(NewsletterCampaign::STATUS_PARTIAL)
        ->and($campaign->sent_count)->toBe(1)
        ->and($campaign->failed_count)->toBe(1)
        ->and($failure->email)->toBe('joao@example.com');

    app(NewsletterSender::class)->retryFailed($campaign);

    expect($campaign->refresh()->status)->toBe(NewsletterCampaign::STATUS_SENDING)
        ->and($campaign->recipients()->where('status', NewsletterCampaignRecipient::STATUS_PENDING)->pluck('email')->all())->toBe(['joao@example.com']);
});

test('envio grande sai em lotes pelo poll da tela', function () {
    Mail::fake();

    foreach (range(1, 15) as $i) {
        Newsletter::create(['name' => "Pessoa {$i}", 'email' => "pessoa{$i}@example.com", 'is_active' => true]);
    }

    $component = fillMessage(livewire(SendNewsletter::class))->call('review')->call('send');

    expect(NewsletterCampaign::sole()->sent_count)->toBe(NewsletterSender::BATCH_SIZE);
    $component->assertSee('Enviando mensagem…')->assertSeeHtml('wire:poll.1s="processNextBatch"');

    $component->call('processNextBatch')->assertSee('Mensagem enviada');

    expect(NewsletterCampaign::sole()->sent_count)->toBe(17);
    Mail::assertSent(NewsletterMail::class, 17);
});

test('conteúdo é limpo antes de enviar', function () {
    $clean = NewsletterContent::sanitize('<div onclick="x()">Oi<script>alert(1)</script> <a href="javascript:alert(1)">link</a> <a href="https://codhous.com">site</a></div><iframe src="https://evil.test"></iframe>');

    expect($clean)
        ->not->toContain('<script')
        ->not->toContain('onclick')
        ->not->toContain('javascript:')
        ->not->toContain('<iframe')
        ->toContain('href="https://codhous.com"')
        ->and(NewsletterContent::forEmail($clean))->toContain('style="color:#2350f5');
});

test('e-mail leva link e cabeçalho para cancelar a inscrição', function () {
    $campaign = NewsletterCampaign::create([
        'idempotency_key' => 'k1', 'subject' => 'Teste', 'body' => '<p>Oi</p>', 'content_hash' => 'h', 'recipients_count' => 1,
    ]);
    $recipient = $campaign->recipients()->create(['email' => 'ana@example.com', 'name' => 'Ana', 'newsletter_id' => Newsletter::first()->id]);

    $mail = new NewsletterMail($campaign, $recipient);

    $mail->assertSeeInHtml('Cancelar inscrição');
    expect($mail->headers()->text)->toHaveKeys(['List-Unsubscribe', 'List-Unsubscribe-Post']);
});

test('link de cancelamento pede confirmação e desativa o inscrito', function () {
    $subscriber = Newsletter::first();
    $url = URL::signedRoute('newsletter.unsubscribe', ['newsletter' => $subscriber->id]);

    $this->get($url)->assertOk()->assertSee('Cancelar inscrição');
    expect($subscriber->refresh()->is_active)->toBeTrue();

    $this->post($url)->assertOk()->assertSee('Inscrição cancelada');
    expect($subscriber->refresh()->is_active)->toBeFalse();

    $this->post(route('newsletter.unsubscribe.store', ['newsletter' => $subscriber->id]))->assertForbidden();
});

test('histórico lista envios e mostra falhas', function () {
    $campaign = NewsletterCampaign::create([
        'idempotency_key' => 'k2', 'subject' => 'Comunicado de outubro', 'body' => '<p>Oi</p>', 'content_hash' => 'h',
        'status' => NewsletterCampaign::STATUS_PARTIAL, 'recipients_count' => 2, 'sent_count' => 1, 'failed_count' => 1,
    ]);
    $campaign->recipients()->create(['email' => 'falhou@example.com', 'status' => NewsletterCampaignRecipient::STATUS_FAILED, 'error' => 'Caixa cheia']);

    livewire(ListNewsletterCampaigns::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$campaign])
        ->assertSee('Enviado com falhas');

    livewire(ViewNewsletterCampaign::class, ['record' => $campaign->getRouteKey()])
        ->assertOk()
        ->assertSee('falhou@example.com')
        ->assertSee('Caixa cheia');
});
