<x-filament-panels::page class="fi-team-page">
    @include('filament.team.styles')

    @php $stats = $this->getTeamStats(); @endphp

    <div class="tm cx-ui">
        <div class="cx-kpis-ctn">
            <section class="cx-kpis" aria-label="Resumo da equipe">
                <x-dashboard.kpi label="Usuários ativos" :value="$stats['active']['value']" :note="$stats['active']['note']" :tone="$stats['active']['tone']" :url="'?aba=usuarios'" wire:click.prevent="$set('section', 'usuarios')" />
                <x-dashboard.kpi label="Convites pendentes" :value="$stats['pending']['value']" :note="$stats['pending']['note']" :tone="$stats['pending']['tone']" :url="'?aba=convites'" wire:click.prevent="$set('section', 'convites')" />
                <x-dashboard.kpi label="Perfis de acesso" :value="$stats['roles']['value']" :note="$stats['roles']['note']" :tone="$stats['roles']['tone']" :url="'?aba=perfis'" wire:click.prevent="$set('section', 'perfis')" />
            </section>
        </div>

        <section class="tm-panel">
            <nav class="tm-tabs" role="tablist" aria-label="Seções da equipe">
                @foreach (\App\Filament\Resources\UserResource\Pages\ListUsers::SECTIONS as $key => $item)
                    <button
                        type="button"
                        role="tab"
                        id="tm-tab-{{ $key }}"
                        aria-controls="tm-panel-{{ $key }}"
                        aria-selected="{{ $this->section === $key ? 'true' : 'false' }}"
                        wire:click="$set('section', '{{ $key }}')"
                        @class(['tm-tab', 'is-active' => $this->section === $key])
                    >
                        <x-filament::icon :icon="$item['icon']" class="cx-icon-sm" aria-hidden="true" />
                        {{ $item['label'] }}
                        @if ($key === 'convites' && $stats['pending']['value'] !== '0')
                            <span class="tm-tab-count">{{ $stats['pending']['value'] }}</span>
                        @endif
                    </button>
                @endforeach
            </nav>

            <div class="tm-body" role="tabpanel" id="tm-panel-{{ $this->section }}" aria-labelledby="tm-tab-{{ $this->section }}" wire:loading.class="is-loading" wire:target="section">
                @switch($this->section)
                    @case('convites')
                        @livewire(\App\Livewire\Team\InvitationsTable::class, key('team-invitations'))
                        @break
                    @case('perfis')
                        @livewire(\App\Livewire\Team\RolesPanel::class, key('team-roles'))
                        @break
                    @case('logs')
                        @livewire(\App\Livewire\Team\AccessLogTable::class, key('team-logs'))
                        @break
                    @default
                        {{ $this->table }}
                @endswitch
            </div>
        </section>
    </div>
</x-filament-panels::page>
