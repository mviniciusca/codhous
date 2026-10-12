{{--
    Enviar mensagem (newsletter): etapas Conteúdo → Destinatários → Conclusão.
    Editor à esquerda e prévia do e-mail à direita; barra inferior com os destinatários e a ação principal.
    Estilos em filament/newsletter/styles (prefixo nl-; tokens de filament/admin/ui).
--}}
@php
    $step = $this->step;
    $activeCount = $this->getActiveSubscribersCount();
    $campaign = $step === 3 ? $this->getCampaign() : null;
    $duplicate = $step === 2 ? $this->getDuplicate() : null;
    $previewHtml = $step < 3 ? $this->getPreviewHtml() : null;
    $countLabel = number_format($activeCount, 0, ',', '.');

    $steps = [
        1 => ['Conteúdo', 'Crie sua mensagem'],
        2 => ['Destinatários', 'Revise e confirme o envio'],
        3 => ['Conclusão', 'Acompanhe o resultado'],
    ];
@endphp

<x-filament-panels::page>
    @include('filament.newsletter.styles')

    <div class="cx-ui nl-host">
        <div class="nl">
            {{-- Etapas --}}
            <ol class="nl-steps" aria-label="Etapas do envio">
                @foreach ($steps as $number => [$label, $description])
                    @php $state = $number < $step ? 'done' : ($number === $step ? 'current' : 'next'); @endphp
                    <li class="nl-step is-{{ $state }}" @if ($state === 'current') aria-current="step" @endif>
                        <span class="nl-step-num" aria-hidden="true">
                            @if ($state === 'done')
                                <x-filament::icon icon="heroicon-m-check" class="cx-icon-sm" />
                            @else
                                {{ $number }}
                            @endif
                        </span>
                        <span class="nl-step-text">
                            <span class="nl-step-label">{{ $label }}</span>
                            <span class="nl-step-desc">{{ $description }}</span>
                        </span>
                        <span class="cx-sr">{{ $state === 'done' ? '(concluída)' : ($state === 'current' ? '(etapa atual)' : '') }}</span>
                    </li>
                @endforeach
            </ol>

            @if ($step < 3)
                <div class="nl-work">
                    {{-- Coluna esquerda: editor ou revisão --}}
                    <section class="nl-panel" aria-labelledby="nl-left-title">
                        @if ($step === 1)
                            <header class="nl-panel-head">
                                <h2 id="nl-left-title" class="nl-panel-title">Conteúdo da mensagem</h2>
                                <p class="nl-panel-desc">Preencha as informações do e-mail que será enviado.</p>
                            </header>
                            <div class="nl-panel-body nl-form">
                                {{ $this->form }}
                            </div>
                        @else
                            <header class="nl-panel-head">
                                <h2 id="nl-left-title" class="nl-panel-title">Revise antes de enviar</h2>
                                <p class="nl-panel-desc">Confira o resumo. Depois de confirmado, o envio não pode ser desfeito.</p>
                            </header>
                            <div class="nl-panel-body">
                                <dl class="nl-summary">
                                    <div class="nl-summary-row">
                                        <dt><x-filament::icon icon="heroicon-o-pencil" class="cx-icon-sm" /> Assunto</dt>
                                        <dd class="nl-summary-strong">{{ $this->data['subject'] ?? '' }}</dd>
                                    </div>
                                    <div class="nl-summary-row">
                                        <dt><x-filament::icon icon="heroicon-o-at-symbol" class="cx-icon-sm" /> Remetente</dt>
                                        <dd>{{ $this->getSenderLabel() }}</dd>
                                    </div>
                                    <div class="nl-summary-row">
                                        <dt><x-filament::icon icon="heroicon-o-users" class="cx-icon-sm" /> Destinatários</dt>
                                        <dd>
                                            <span class="nl-summary-strong">{{ $countLabel }} {{ $activeCount === 1 ? 'inscrito ativo' : 'inscritos ativos' }}</span>
                                            <ul class="nl-people">
                                                @foreach ($this->getRecipientSample() as $person)
                                                    <li>
                                                        <span class="nl-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($person->name ?: $person->email, 0, 1)) }}</span>
                                                        <span class="nl-person">
                                                            <span>{{ $person->name }}</span>
                                                            <span class="nl-muted">{{ $person->email }}</span>
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            @if ($activeCount > 4)
                                                <span class="nl-muted">e mais {{ number_format($activeCount - 4, 0, ',', '.') }}.</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="nl-summary-row">
                                        <dt><x-filament::icon icon="heroicon-o-document-text" class="cx-icon-sm" /> Conteúdo</dt>
                                        <dd>
                                            {{ number_format(\App\Services\Newsletter\NewsletterContent::textLength($this->data['body'] ?? ''), 0, ',', '.') }} caracteres
                                            · {{ filled($this->data['banner'] ?? null) ? 'com imagem de topo' : 'sem imagem de topo' }}
                                        </dd>
                                    </div>
                                </dl>

                                @if ($duplicate)
                                    <div class="nl-alert cx-tone-warning" role="alert">
                                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="cx-icon" />
                                        <div class="nl-alert-body">
                                            <strong>Esta mesma mensagem já foi enviada</strong>
                                            <span>Um envio idêntico foi feito em {{ $duplicate->created_at->format('d/m/Y \à\s H:i') }} ({{ $duplicate->statusLabel() }}, {{ $duplicate->sent_count }} {{ $duplicate->sent_count === 1 ? 'destinatário' : 'destinatários' }}).</span>
                                            <label class="nl-check">
                                                <input type="checkbox" wire:model="confirmDuplicate">
                                                <span>Entendo e quero enviar novamente</span>
                                            </label>
                                            @error('confirmDuplicate')
                                                <span class="nl-error">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                <p class="nl-note">
                                    <x-filament::icon icon="heroicon-m-information-circle" class="cx-icon-sm" />
                                    O envio usa o servidor SMTP configurado e acontece em lotes. Mantenha a página aberta até a conclusão. Cada e-mail leva o link para cancelar a inscrição.
                                </p>
                            </div>
                        @endif
                    </section>

                    {{-- Coluna direita: prévia --}}
                    <section
                        class="nl-panel nl-preview"
                        aria-labelledby="nl-preview-title"
                        x-data="{
                            device: 'desktop',
                            widths: { desktop: '100%', tablet: '680px', mobile: '375px' },
                            init() {
                                try { this.device = localStorage.getItem('nl-preview-device') || 'desktop' } catch (e) {}
                                if (! this.widths[this.device]) this.device = 'desktop'
                            },
                            setDevice(value) {
                                this.device = value
                                try { localStorage.setItem('nl-preview-device', value) } catch (e) {}
                            },
                        }"
                    >
                        <header class="nl-panel-head nl-preview-head">
                            <div>
                                <h2 id="nl-preview-title" class="nl-panel-title">Pré-visualização do e-mail</h2>
                                <p class="nl-panel-desc">Veja como sua mensagem será exibida para os inscritos.</p>
                            </div>
                            <div class="nl-seg" role="group" aria-label="Largura da prévia">
                                @foreach (['desktop' => ['Desktop', 'heroicon-m-computer-desktop'], 'tablet' => ['Tablet', 'heroicon-m-device-tablet'], 'mobile' => ['Celular', 'heroicon-m-device-phone-mobile']] as $device => [$deviceLabel, $deviceIcon])
                                    <button
                                        type="button"
                                        class="nl-seg-btn"
                                        :class="{ 'is-active': device === '{{ $device }}' }"
                                        :aria-pressed="(device === '{{ $device }}').toString()"
                                        x-on:click="setDevice('{{ $device }}')"
                                    >
                                        <x-filament::icon :icon="$deviceIcon" />
                                        <span class="nl-seg-label">{{ $deviceLabel }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </header>

                        <div
                            class="nl-stage"
                            wire:ignore
                            x-data="{
                                update(html) {
                                    const frame = this.$refs.frame
                                    let top = 0
                                    try { top = frame.contentWindow.scrollY } catch (e) {}
                                    frame.onload = () => { try { frame.contentWindow.scrollTo(0, top) } catch (e) {} }
                                    frame.srcdoc = html
                                },
                            }"
                            x-init="update(@js($previewHtml))"
                            x-on:nl-preview.window="update($event.detail.html)"
                        >
                            <div class="nl-frame" :class="'is-' + device" :style="{ width: widths[device] }">
                                <iframe x-ref="frame" sandbox="allow-same-origin" title="Pré-visualização do e-mail" tabindex="-1"></iframe>
                            </div>
                        </div>

                        {{-- Cada mudança no conteúdo troca esta chave; o x-init manda o HTML novo para a prévia sem recarregar a tela --}}
                        <div hidden wire:key="nl-src-{{ md5($previewHtml) }}" x-data x-init="$dispatch('nl-preview', { html: @js($previewHtml) })"></div>
                    </section>
                </div>

                {{-- Barra inferior --}}
                <div class="nl-bar">
                    <div class="nl-bar-count">
                        <span class="nl-bar-icon" aria-hidden="true"><x-filament::icon icon="heroicon-o-users" class="cx-icon" /></span>
                        <span>
                            <span class="nl-bar-label">Destinatários</span>
                            <span class="nl-bar-value">{{ $countLabel }}</span>
                            <span class="nl-bar-label">{{ $activeCount === 1 ? 'inscrito ativo' : 'inscritos ativos' }}</span>
                        </span>
                    </div>

                    <p class="nl-bar-text">
                        @if ($activeCount === 0)
                            Nenhum inscrito ativo no momento. A mensagem não terá destinatários.
                        @elseif ($step === 1)
                            A mensagem será enviada para todos os inscritos ativos na sua newsletter.
                        @else
                            Ao confirmar, a mensagem segue para {{ $countLabel }} {{ $activeCount === 1 ? 'pessoa' : 'pessoas' }}. Essa ação não pode ser desfeita.
                        @endif
                    </p>

                    <div class="nl-bar-actions">
                        @if ($step === 1)
                            <x-filament::button
                                tag="a"
                                color="gray"
                                outlined
                                :href="\App\Filament\Resources\NewsletterResource::getUrl('index')"
                                target="_blank"
                                icon="heroicon-m-arrow-top-right-on-square"
                                icon-position="after"
                            >
                                Ver inscritos
                            </x-filament::button>
                            <x-filament::button
                                wire:click="review"
                                icon="heroicon-m-arrow-right"
                                icon-position="after"
                                :disabled="$activeCount === 0"
                            >
                                Revisar e confirmar envio
                            </x-filament::button>
                        @else
                            <x-filament::button
                                color="gray"
                                outlined
                                wire:click="backToEdit"
                                wire:loading.attr="disabled"
                                wire:target="send"
                                icon="heroicon-m-arrow-left"
                            >
                                Voltar e editar
                            </x-filament::button>
                            <x-filament::button
                                wire:click="send"
                                wire:loading.attr="disabled"
                                wire:target="send"
                                icon="heroicon-m-paper-airplane"
                            >
                                Confirmar e enviar para {{ $countLabel }}
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            @else
                {{-- Conclusão --}}
                @if (! $campaign)
                    <div class="nl-panel">
                        <div class="cx-empty cx-tone-gray">
                            <span class="cx-empty-icon"><x-filament::icon icon="heroicon-o-question-mark-circle" class="cx-icon" /></span>
                            <span class="cx-empty-title">Envio não encontrado</span>
                            <span class="cx-empty-desc">Ele pode ter sido removido. Comece uma nova mensagem.</span>
                            <x-filament::button wire:click="startNew" icon="heroicon-m-pencil-square">Escrever nova mensagem</x-filament::button>
                        </div>
                    </div>
                @else
                    @php
                        $sending = $campaign->isSending();
                        [$tone, $icon, $title, $text] = match ($campaign->status) {
                            \App\Models\NewsletterCampaign::STATUS_SENT => ['success', 'heroicon-o-check-circle', 'Mensagem enviada', 'Todos os destinatários foram aceitos pelo servidor de e-mail.'],
                            \App\Models\NewsletterCampaign::STATUS_PARTIAL => ['warning', 'heroicon-o-exclamation-triangle', 'Mensagem enviada com falhas', 'Parte dos e-mails não foi aceita. Veja os motivos abaixo e tente de novo só para quem falhou.'],
                            \App\Models\NewsletterCampaign::STATUS_FAILED => ['danger', 'heroicon-o-x-circle', 'O envio falhou', 'Nenhum e-mail foi aceito pelo servidor. Confira as configurações de SMTP e tente novamente.'],
                            default => ['primary', null, 'Enviando mensagem…', 'Mantenha esta página aberta. Os e-mails saem em lotes pelo SMTP configurado.'],
                        };
                        $failures = $campaign->failed_count > 0 ? $this->getFailures() : collect();
                    @endphp

                    <section
                        class="nl-panel nl-result cx-tone-{{ $tone }}"
                        aria-labelledby="nl-result-title"
                        wire:key="nl-result-{{ $campaign->id }}-{{ $sending ? 'sending' : 'done' }}"
                        @if ($sending) wire:poll.1s="processNextBatch" @endif
                        {{-- Avisa antes de fechar a aba no meio do envio --}}
                        x-data="{ init() { if (@js($sending)) window.onbeforeunload = () => true }, destroy() { window.onbeforeunload = null } }"
                    >
                        <div class="nl-result-head">
                            <span class="nl-result-icon" aria-hidden="true">
                                @if ($icon)
                                    <x-filament::icon :icon="$icon" class="cx-icon" />
                                @else
                                    <x-filament::loading-indicator class="cx-icon" />
                                @endif
                            </span>
                            <div aria-live="polite">
                                <h2 id="nl-result-title" class="nl-result-title">{{ $title }}</h2>
                                <p class="nl-panel-desc">{{ $text }}</p>
                            </div>
                        </div>

                        <div class="nl-result-subject">
                            <span class="nl-muted">Assunto</span>
                            <strong>{{ $campaign->subject }}</strong>
                        </div>

                        <div
                            class="nl-progress"
                            role="progressbar"
                            aria-label="Progresso do envio"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            aria-valuenow="{{ $campaign->progress() }}"
                        >
                            <span style="width: {{ $campaign->progress() }}%"></span>
                        </div>

                        <dl class="nl-stats">
                            <div><dt>Destinatários</dt><dd>{{ number_format($campaign->recipients_count, 0, ',', '.') }}</dd></div>
                            <div class="cx-tone-success"><dt>Enviados</dt><dd>{{ number_format($campaign->sent_count, 0, ',', '.') }}</dd></div>
                            <div class="cx-tone-danger"><dt>Falhas</dt><dd>{{ number_format($campaign->failed_count, 0, ',', '.') }}</dd></div>
                            <div><dt>{{ $sending ? 'Na fila' : 'Concluído em' }}</dt><dd>{{ $sending ? number_format($campaign->pendingCount(), 0, ',', '.') : $campaign->finished_at?->format('d/m H:i') }}</dd></div>
                        </dl>

                        @if ($failures->isNotEmpty())
                            <div class="nl-failures">
                                <h3 class="nl-failures-title">
                                    <x-filament::icon icon="heroicon-m-exclamation-circle" class="cx-icon-sm" />
                                    Destinatários com falha
                                </h3>
                                <div class="nl-table-wrap">
                                    <table class="nl-table">
                                        <thead>
                                            <tr><th scope="col">E-mail</th><th scope="col">Nome</th><th scope="col">Motivo</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($failures as $failure)
                                                <tr>
                                                    <td>{{ $failure->email }}</td>
                                                    <td>{{ $failure->name ?: '—' }}</td>
                                                    <td class="nl-muted">{{ $failure->error }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if ($campaign->failed_count > $failures->count())
                                    <p class="nl-muted">Mostrando {{ $failures->count() }} de {{ $campaign->failed_count }}. A lista completa está no histórico.</p>
                                @endif
                            </div>
                        @endif

                        @unless ($sending)
                            <div class="nl-result-actions">
                                <x-filament::button tag="a" color="gray" outlined :href="\App\Filament\Resources\NewsletterCampaignResource::getUrl('view', ['record' => $campaign])" icon="heroicon-m-clock">
                                    Ver no histórico
                                </x-filament::button>
                                @if ($campaign->failed_count > 0)
                                    <x-filament::button color="gray" wire:click="retryFailed" icon="heroicon-m-arrow-path">
                                        Tentar de novo para {{ $campaign->failed_count }} {{ $campaign->failed_count === 1 ? 'falha' : 'falhas' }}
                                    </x-filament::button>
                                @endif
                                <x-filament::button wire:click="startNew" icon="heroicon-m-pencil-square">
                                    Escrever nova mensagem
                                </x-filament::button>
                            </div>
                        @endunless
                    </section>
                @endif
            @endif
        </div>
    </div>
</x-filament-panels::page>
