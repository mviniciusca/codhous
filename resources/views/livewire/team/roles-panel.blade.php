<div class="tm-roles">
    <div class="tm-roles-head">
        <p>Cada perfil libera um conjunto de áreas do painel. As regras valem no servidor: o que não está liberado não aparece no menu nem abre pelo link.</p>
        {{ $this->createRoleAction }}
    </div>

    <div class="tm-role-grid">
        @foreach ($this->roles as $role)
            @php $granted = $role->areas()->map->value; @endphp
            <article @class(['tm-role', 'is-system' => $role->is_system]) wire:key="role-{{ $role->id }}">
                <header class="tm-role-top">
                    <span class="tm-role-icon" aria-hidden="true">
                        <x-filament::icon :icon="$role->is_system ? 'heroicon-o-shield-check' : 'heroicon-o-identification'" class="cx-icon" />
                    </span>
                    <div class="tm-role-titles">
                        <h3>
                            {{ $role->name }}
                            @if ($role->is_system)
                                <span class="tm-role-lock" title="Perfil do sistema: não pode ser alterado">
                                    <x-filament::icon icon="heroicon-m-lock-closed" class="cx-icon-xs" aria-hidden="true" />
                                    Sistema
                                </span>
                            @endif
                        </h3>
                        <p>{{ $role->description ?: 'Sem descrição' }}</p>
                    </div>
                </header>

                <ul class="tm-areas" role="list" aria-label="Áreas do perfil {{ $role->name }}">
                    @foreach ($areas as $area)
                        @php $on = $granted->contains($area->value); @endphp
                        <li @class(['tm-area', 'is-on' => $on])>
                            <x-filament::icon :icon="$on ? 'heroicon-m-check-circle' : 'heroicon-m-minus-circle'" class="cx-icon-sm" aria-hidden="true" />
                            <span>{{ $area->label() }}</span>
                            <span class="cx-sr">{{ $on ? 'liberado' : 'sem acesso' }}</span>
                        </li>
                    @endforeach
                </ul>

                <footer class="tm-role-foot">
                    <span class="tm-role-count">
                        <x-filament::icon icon="heroicon-m-users" class="cx-icon-sm" aria-hidden="true" />
                        {{ $role->users_count }} {{ $role->users_count === 1 ? 'pessoa' : 'pessoas' }}
                    </span>
                    @unless ($role->is_system)
                        <span class="tm-role-actions">
                            {{ ($this->editRoleAction)(['role' => $role->id]) }}
                            @if ($role->users_count === 0)
                                {{ ($this->deleteRoleAction)(['role' => $role->id]) }}
                            @endif
                        </span>
                    @endunless
                </footer>
            </article>
        @endforeach
    </div>

    <x-filament-actions::modals />
</div>
