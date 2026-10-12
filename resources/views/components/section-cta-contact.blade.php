@props([
    'data' => [],
    'image' => null,
    'imageSize' => 155,
    'imageOffsetX' => 0,
    'imageOffsetY' => 0,
    'showFeatures' => true,
    'title' => null,
    'subtitle' => null,
    'buttonLabel' => null,
    'buttonUrl' => null,
])
@if(!\App\Models\ContentSection::isHidden('cta_contact'))
{{-- O cartão já é escuro; a seção em volta segue o fundo escolhido (padrão transparente) --}}
<x-ui.section :data="\Illuminate\Support\Arr::except((array) $data, ['text_color'])" spacing="sm" background="bg-transparent" class="!overflow-visible">
    <x-ui.contact-cta
        :image="$image"
        :image-size="$imageSize"
        :image-offset-x="$imageOffsetX"
        :image-offset-y="$imageOffsetY"
        :show-features="$showFeatures"
        :title="$title"
        :subtitle="$subtitle"
        :button-label="$buttonLabel"
        :button-url="$buttonUrl"
    />
</x-ui.section>
@endif
