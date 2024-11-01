<div class="">
    @foreach ($data as $budget)

    <div class="mx-auto p-2 space-y-2">
        <div class="relative border-b-gray-500 border-b">
            <div class="rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <h2
                        class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $budget->user->name }}
                    </h2>
                    <span class="fi-fo-field-wrp-helper-text break-words text-sm text-gray-500">2 horas atrás</span>
                </div>
                <p
                    class="mt-2 fi-section-header-description overflow-hidden break-words text-sm text-gray-500 dark:text-gray-400">
                    Descrição detalhada do evento ou postagem. Este é o conteúdo principal que descreve o
                    que aconteceu nesse momento específico na timeline.</p>
            </div>
        </div>


    </div>



    @endforeach
</div>