@props(['image' => null, 'imageSize' => 155, 'imageOffsetX' => 0, 'imageOffsetY' => 0, 'showFeatures' => true])
@if(!\App\Models\ContentSection::isHidden('cta_contact'))
<section class="py-8 lg:py-12">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <x-ui.contact-cta :image="$image" :image-size="$imageSize" :image-offset-x="$imageOffsetX" :image-offset-y="$imageOffsetY" :show-features="$showFeatures" />
    </div>
</section>
@endif
