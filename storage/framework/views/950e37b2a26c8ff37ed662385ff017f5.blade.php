<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
@props(['hasInlineLabel','id','label','labelSrOnly','helperText','hint','hintActions','hintColor','hintIcon','hintIconTooltip','statePath'])
<x-filament-forms::field-wrapper :has-inline-label="$hasInlineLabel" :id="$id" :label="$label" :label-sr-only="$labelSrOnly" :helper-text="$helperText" :hint="$hint" :hint-actions="$hintActions" :hint-color="$hintColor" :hint-icon="$hintIcon" :hint-icon-tooltip="$hintIconTooltip" :state-path="$statePath" >

{{ $slot ?? "" }}
</x-filament-forms::field-wrapper>