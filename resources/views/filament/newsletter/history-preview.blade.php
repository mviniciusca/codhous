{{-- Mensagem como foi enviada (iframe isolado, sem scripts) --}}
@php
    /** @var \App\Models\NewsletterCampaign $record */
    $record = $getRecord();
    $html = \App\Services\Newsletter\NewsletterEmail::render($record->subject, $record->body, $record->banner_path);
@endphp
<iframe
    title="Mensagem enviada: {{ $record->subject }}"
    sandbox="allow-same-origin"
    srcdoc="{{ $html }}"
    style="display:block;width:100%;height:640px;border:0;border-radius:12px;background:#f3f4f6;"
></iframe>
