<div>
    @if ($data->whatsapp['status'] && $data->whatsapp['phone'])
    <a target="_new"
        href="{{ env('WPP_URL') . env('COUNTRY_CODE') . $data->whatsapp['phone'] .  '?text=' . $data->whatsapp['message']}}">
        <div style="background-color:{{ $data->whatsapp['color'] ?? ' #25d366'}}"
            class="w-16 h-16  hover:bg-green-500 cursor-pointer fixed z-50  bottom-0 mb-5 mr-5 animate-pulse grid items-center justify-center right-0 text-white text-center rounded-full">
            <ion-icon class="text-4xl p-2" name="{{ $data->whatsapp['icon'] ?? 'logo-whatsapp' }}"></ion-icon>
        </div>
    </a>
    @endif
</div>