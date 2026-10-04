@props([
    'data' => []
])

@php
    $bgImg = !empty($data['background_image']) ? \Illuminate\Support\Facades\Storage::url($data['background_image']) : null;
    $bgFitOption = $data['background_image_fit'] ?? 'cover';
    $bgScale = $data['background_image_scale'] ?? '50';
    $bgFit = $bgFitOption === 'custom' ? "{$bgScale}% auto" : $bgFitOption;
    $bgPos = $data['background_image_position'] ?? 'center';
    $bgOp = ($data['background_image_opacity'] ?? '100') / 100;
    $bgPullUpAmount = (int) ($data['background_image_pull_up'] ?? 0);
    $bgDivClasses = 'absolute inset-x-0 z-0 pointer-events-none bg-no-repeat';
    
    $bgPosParts = explode(' ', $bgPos);
    $bgPosHorizontal = $bgPosParts[0];
    if ($bgPullUpAmount > 0) {
        $topStyle = "-{$bgPullUpAmount}%";
        $bottomStyle = "0";
        $bgPositionStyle = $bgPosHorizontal . ' bottom';
    } elseif ($bgPullUpAmount < 0) {
        $topStyle = "0";
        $bottomStyle = $bgPullUpAmount . "%";
        $bgPositionStyle = $bgPosHorizontal . ' top';
    } else {
        $topStyle = "0";
        $bottomStyle = "0";
        $bgPositionStyle = $bgPos;
    }

    $overlayEnabled = $data['background_overlay_enabled'] ?? false;
    $overlayType = $data['background_overlay_type'] ?? 'dark';
    $overlayOpacity = ($data['background_overlay_opacity'] ?? '50') / 100;
    $overlayColor = $overlayType === 'light' ? '255, 255, 255' : '0, 0, 0';
@endphp

@if($bgImg)
    <div class="{{ $bgDivClasses }}" style="top: {{ $topStyle }}; bottom: {{ $bottomStyle }}; background-image: url('{{ $bgImg }}'); background-size: {{ $bgFit }}; background-position: {{ $bgPositionStyle }}; opacity: {{ $bgOp }};"></div>
@endif

@if($overlayEnabled)
    <div class="absolute inset-0 z-[5] pointer-events-none" style="background-color: rgba({{ $overlayColor }}, {{ $overlayOpacity }});"></div>
@endif
