{{-- Campo de cor: papéis da paleta (vinculados), cores recentes e cor personalizada.
     $model: expressão JS do ColorRef; $set: função JS que recebe o novo ColorRef. --}}
<div class="ps-field">
    <div class="ps-section-title" style="margin-bottom: 2px">
        <span class="ps-label">{{ $label }}</span>
        <span class="ps-chip" :class="isLinked({!! $model !!}) && 'linked'"
              x-text="isLinked({!! $model !!}) ? 'Paleta · ' + (colorRoles[({!! $model !!}).role] ?? '') : (({!! $model !!}) == null ? 'Transparente' : 'Personalizada')"></span>
    </div>
    <div class="ps-color-current">
        <input type="color" class="ps-color-input" aria-label="{{ $label }}: escolher cor"
               :value="resolve({!! $model !!}) || '#FFFFFF'"
               @change="({!! $set !!})($event.target.value.toUpperCase())">
        <input type="text" class="ps-input" style="width: 96px" maxlength="7" aria-label="{{ $label }}: código hexadecimal"
               :value="resolve({!! $model !!}) ?? ''"
               @change="const v = $event.target.value.trim().replace('#', ''); /^[0-9a-f]{6}$/i.test(v) ? ({!! $set !!})('#' + v.toUpperCase()) : ($event.target.value = resolve({!! $model !!}) ?? '')">
        <template x-if="isLinked({!! $model !!})">
            <button type="button" class="ps-link" title="Manter esta cor mesmo se a paleta mudar" @click="({!! $set !!})(resolve({!! $model !!}))">Desvincular</button>
        </template>
    </div>
    <div class="ps-swatches" style="margin-top: 8px" role="group" aria-label="Cores da paleta">
        <template x-for="(roleLabel, role) in colorRoles" :key="role">
            <button type="button" class="ps-swatch" :style="{ background: doc.palette.colors[role] }"
                    :title="'Paleta · ' + roleLabel" :aria-label="'Usar ' + roleLabel + ' da paleta'"
                    :aria-pressed="isLinked({!! $model !!}) && ({!! $model !!}).role === role"
                    @click="({!! $set !!})({ role })"></button>
        </template>
        @if ($transparent ?? false)
            <button type="button" class="ps-swatch transparent" title="Transparente" aria-label="Transparente"
                    :aria-pressed="({!! $model !!}) == null" @click="({!! $set !!})(null)"></button>
        @endif
    </div>
    <template x-if="recentColors.length">
        <div class="ps-swatches" style="margin-top: 6px" role="group" aria-label="Cores recentes">
            <template x-for="hex in recentColors" :key="hex">
                <button type="button" class="ps-swatch" :style="{ background: hex }" :title="hex" :aria-label="'Usar ' + hex"
                        :aria-pressed="({!! $model !!}) === hex" @click="({!! $set !!})(hex)"></button>
            </template>
        </div>
    </template>
</div>
