<x-filament-panels::page>
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@400;700;900&family=Oswald:wght@400;700&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Poppins:wght@400;700;900&family=Roboto:wght@400;700;900&family=Lato:wght@400;700;900&family=Raleway:wght@400;700;900&family=Ubuntu:wght@400;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 280px;
            --gallery-width: 88px;
            --accent-color: #fbbf24;

            /* Light Theme Defaults */
            --bg-base: #f1f5f9;
            --bg-surface: #ffffff;
            --bg-card: #f8fafc;
            --border-subtle: rgba(0, 0, 0, 0.08);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --dot-color: rgba(0, 0, 0, 0.1);
            --command-bg: rgba(255, 255, 255, 0.9);
            --input-text: #1e293b;
        }

        .dark {
            --bg-base: #09090b;
            --bg-surface: #121217;
            --bg-card: rgba(255, 255, 255, 0.03);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --text-main: #ffffff;
            --text-muted: #a1a1aa;
            --dot-color: rgba(255, 255, 255, 0.1);
            --command-bg: rgba(18, 18, 23, 0.85);
            --input-text: #ffffff;
        }

        .studio-layout {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr var(--gallery-width);
            background: var(--bg-base);
            color: var(--text-main);
            z-index: 40;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            transition: background 0.3s ease;
        }

        /* ── Sidebar ── */
        .sidebar-controls {
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: var(--bg-surface);
        }

        /* Header */
        .sb-header {
            padding: 14px 16px 12px;
            flex-shrink: 0;
            border-bottom: 1px solid var(--border-subtle);
        }

        .sb-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sb-brand-icon {
            width: 28px;
            height: 28px;
            background: var(--accent-color);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sb-brand-text {
            flex: 1;
            min-width: 0;
        }

        .sb-brand-text strong {
            display: block;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
            line-height: 1;
        }

        .sb-brand-text span {
            font-size: 9px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .sb-back {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            border: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            flex-shrink: 0;
            transition: 0.15s;
        }
        .sb-back:hover { color: var(--accent-color); border-color: var(--accent-color); }

        /* Scroll body */
        .sb-body {
            flex: 1;
            overflow-y: auto;
            padding: 14px 16px 20px;
            scrollbar-width: none;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .sb-body::-webkit-scrollbar { display: none; }

        /* Section title */
        .section-title {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.1em;
            margin-bottom: 8px;
            display: block;
        }

        /* Section block */
        .sb-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            transition: 0.2s;
        }
        .back-link:hover { color: var(--accent-color); }



        /* Custom Inputs */
        .studio-select {
            width: 100%;
            background-color: var(--bg-base);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 12px;
            border: 1px solid var(--border-subtle) !important;
            border-radius: 8px !important;
            padding: 7px 28px 7px 10px !important;
            font-size: 11px !important;
            font-weight: 600;
            color: var(--text-main);
            appearance: none !important;
            cursor: pointer;
            transition: border-color 0.15s;
        }

        .studio-select:focus {
            border-color: var(--accent-color) !important;
            outline: none;
        }

        .custom-range {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            background: var(--border-subtle);
            border-radius: 2px;
            outline: none;
        }

        .custom-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            background: var(--accent-color);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(251, 191, 36, 0.3);
        }

        /* Canvas Area */
        .preview-main {
            position: relative;
            background-color: var(--bg-base);
            background-image:
                radial-gradient(var(--dot-color) 1.5px, transparent 1.5px);
            background-size: 24px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            overflow: auto;
        }

        .canvas-container {
            position: relative;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.3);
            border-radius: 4px;
            overflow: hidden;
            transition: 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dark .canvas-container {
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.7);
        }

        .canvas-box {
            width: 500px;
            height: 500px;
            background-color: #ffffff;
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            padding: 48px;
            transition: all 0.4s ease;
        }

        .dark .canvas-box {
            background-color: #18181b;
        }

        .canvas-overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
        }

        .canvas-pattern {
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            mask-repeat: repeat;
            -webkit-mask-repeat: repeat;
        }

        .canvas-text {
            z-index: 3;
            width: 100%;
            word-wrap: break-word;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Command Bar */
        .command-bar-wrapper {
            position: absolute;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            z-index: 100;
        }

        .preset-strip {
            display: flex;
            gap: 4px;
            background: var(--command-bg);
            backdrop-filter: blur(16px);
            padding: 4px;
            border-radius: 12px;
            border: 1px solid var(--border-subtle);
            opacity: 0;
            transform: translateY(10px);
            transition: 0.3s;
            pointer-events: none;
        }

        .command-bar-wrapper:hover .preset-strip {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .btn-preset-mini {
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-muted);
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .btn-preset-mini.active {
            background: var(--accent-color);
            color: #000;
        }

        .command-bar {
            width: 720px;
            height: 64px;
            background: var(--command-bg);
            backdrop-filter: blur(24px);
            border: 1px solid var(--border-subtle);
            border-radius: 32px;
            display: flex;
            align-items: center;
            padding: 0 8px 0 24px;
            gap: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        }

        .dark .command-bar {
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }

        .prompt-input {
            flex: 1;
            background: transparent !important;
            border: none !important;
            color: var(--input-text) !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            outline: none !important;
        }

        .generate-btn {
            background: var(--accent-color);
            color: #000;
            height: 48px;
            padding: 0 24px;
            border-radius: 24px;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .generate-btn:hover {
            transform: scale(1.02);
            background: #fcd34d;
        }

        /* Gallery */
        .sidebar-gallery {
            padding: 14px 10px;
            border-left: 1px solid var(--border-subtle);
            background: var(--bg-surface);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .asset-thumb {
            width: 58px;
            height: 58px;
            border-radius: 11px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: 0.25s;
            background: var(--bg-base);
        }

        .asset-thumb.active {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(251,191,36,0.3);
        }

        .btn-upload {
            width: 58px;
            height: 58px;
            background: var(--bg-base);
            border: 1.5px dashed var(--border-subtle);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: 0.15s;
        }
        .btn-upload:hover { border-color: var(--accent-color); color: var(--accent-color); }

        .pattern-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .btn-toggle {
            aspect-ratio: 1;
            border-radius: 10px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-base);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn-toggle.active {
            border-color: var(--accent-color);
            background: rgba(251, 191, 36, 0.1);
        }

        .btn-toggle img {
            filter: brightness(0);
            opacity: 0.4;
        }

        .dark .btn-toggle img {
            filter: invert(1);
            opacity: 0.6;
        }

        .recent-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .art-card {
            position: relative;
        }

        .art-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            opacity: 0;
            transition: 0.3s;
            z-index: 20;
        }

        .art-card:hover .art-overlay {
            opacity: 1;
        }

        .btn-action-mini {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .btn-action-mini:hover {
            background: var(--accent-color);
            color: #000;
        }

        .btn-action-mini.btn-delete:hover {
            background: #ef4444;
            color: #fff;
        }

        /* Custom UI classes for Studio Editor Sidebar */
        .sb-label {
            font-size: 7.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            color: var(--text-muted) !important;
            display: inline-block;
        }
        .sb-value {
            font-size: 9px !important;
            font-weight: 700 !important;
            color: var(--accent-color) !important;
        }
        .bold-toggle-btn {
            flex: 1;
            height: 28px !important;
            border-radius: 6px;
            border: 1px solid var(--border-subtle);
            font-size: 9px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: 0.15s;
            background: var(--bg-base);
            color: var(--text-muted);
            margin-top: 12px;
        }
        .bold-toggle-btn:hover {
            background: rgba(251, 191, 36, 0.05);
            color: var(--accent-color);
        }
        .bold-toggle-btn.active {
            background: var(--accent-color) !important;
            border-color: var(--accent-color) !important;
            color: #000000 !important;
            box-shadow: 0 1px 3px rgba(251, 191, 36, 0.2);
        }
        .studio-toggle {
            position: relative;
            display: inline-flex;
            width: 32px !important;
            height: 18px !important;
            flex-shrink: 0;
            cursor: pointer;
            border-radius: 9px;
            border: 1.5px solid var(--border-subtle);
            background: var(--bg-base);
            transition: all 0.2s ease;
            align-items: center;
            padding: 0 1px;
        }
        .studio-toggle.active {
            background: var(--accent-color) !important;
            border-color: var(--accent-color) !important;
        }
        .studio-toggle-knob {
            pointer-events: none;
            display: inline-block;
            width: 12px !important;
            height: 12px !important;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            transition: transform 0.2s ease;
            transform: translateX(0);
        }
        .studio-toggle.active .studio-toggle-knob {
            transform: translateX(14px) !important;
        }
        .vignette-btn-group {
            display: flex;
            gap: 6px;
            width: 100%;
        }
        .vignette-btn {
            flex: 1;
            height: 26px !important;
            border-radius: 6px;
            border: 1px solid var(--border-subtle);
            font-size: 8px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.15s;
            background: var(--bg-base);
            color: var(--text-muted);
        }
        .vignette-btn:hover {
            background: rgba(251, 191, 36, 0.05);
            color: var(--accent-color);
        }
        .vignette-btn.active-black {
            background: #000000 !important;
            border-color: #000000 !important;
            color: #ffffff !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .dark .vignette-btn.active-black {
            background: #ffffff !important;
            border-color: #ffffff !important;
            color: #000000 !important;
        }
        .vignette-btn.active-white {
            background: #ffffff !important;
            border-color: var(--border-subtle) !important;
            color: #000000 !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .dark .vignette-btn.active-white {
            background: #27272a !important;
            border-color: #3f3f46 !important;
            color: #ffffff !important;
        }
        .logo-upload-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 32px !important;
            border: 1px dashed var(--border-subtle);
            border-radius: 8px;
            cursor: pointer;
            transition: 0.15s;
            width: 100%;
            background: var(--bg-base);
        }
        .logo-upload-btn:hover {
            border-color: var(--accent-color);
            background: rgba(251, 191, 36, 0.05);
        }
        .logo-upload-text {
            font-size: 8px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: var(--text-muted);
        }
        .logo-upload-btn:hover .logo-upload-text {
            color: var(--accent-color);
        }
    </style>

    <div class="studio-layout">
        {{-- Left Sidebar --}}
        <aside class="sidebar-controls">

            {{-- Header --}}
            <div class="sb-header">
                <div class="sb-brand">
                    <div class="sb-brand-icon">
                        <x-heroicon-o-sparkles class="w-3.5 h-3.5 text-black" />
                    </div>
                    <div class="sb-brand-text">
                        <strong>StudioIA</strong>
                    </div>
                    <a href="{{ filament()->getUrl() }}" class="sb-back" title="Voltar">
                        <x-heroicon-m-arrow-left class="w-3 h-3" />
                    </a>
                </div>
            </div>

            {{-- Scroll Body: todas as seções visíveis --}}
            <div class="sb-body">

                {{-- Tipografia --}}
                <div class="sb-section">
                    <span class="section-title">Tipografia</span>
                    <select wire:model.live="fontFamily" class="studio-select">
                        @foreach($this->fontOptions as $val => $lbl)
                            <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="sb-label">Tamanho</span>
                            <span class="sb-value">{{ $fontSize }}px</span>
                        </div>
                        <input type="range" wire:model.live="fontSize" min="12" max="150" class="custom-range">
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex flex-col gap-1 flex-shrink-0">
                            <span class="sb-label">Cor</span>
                            <input type="color" wire:model.live="textColor"
                                class="w-8 h-8 p-0.5 bg-transparent border border-zinc-200 dark:border-zinc-800 rounded-md cursor-pointer" style="width: 32px; height: 32px;">
                        </div>
                        <div wire:click="$toggle('isBold')" class="bold-toggle-btn {{ $isBold ? 'active' : '' }}" role="button">
                            <span class="text-xs">B</span>
                            <span class="font-normal normal-case text-[9px]">{{ $isBold ? 'Negrito' : 'Normal' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Overlay & Filtros --}}
                <div class="sb-section">
                    <span class="section-title">Overlay &amp; Filtros</span>
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="sb-label">Opacidade Fundo</span>
                            <span class="sb-value">{{ $overlayOpacity }}%</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="color" wire:model.live="overlayColor"
                                class="w-7 h-7 p-0.5 bg-transparent border border-zinc-200 dark:border-zinc-800 rounded-md cursor-pointer flex-shrink-0">
                            <input type="range" wire:model.live="overlayOpacity" min="0" max="100" class="custom-range">
                        </div>
                    </div>
                    <div>
                        <span class="sb-label mb-2 block">Textura</span>
                        <div class="pattern-grid">
                            <div wire:click="$set('pattern', null)" class="btn-toggle cursor-pointer {{ is_null($pattern) ? 'active' : '' }}" role="button">
                                <span class="text-[8px] font-black {{ is_null($pattern) ? 'text-amber-500' : 'text-zinc-400' }}">SEM</span>
                            </div>
                            @foreach(['dots', 'lines', 'grid'] as $p)
                                <div wire:click="$set('pattern', '{{ $p }}')" class="btn-toggle cursor-pointer {{ $pattern === $p ? 'active' : '' }}" role="button">
                                    <img src="/assets/patterns/{{ $p }}.png" class="w-4 h-4 opacity-50">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="sb-label">Vinheta</span>
                        <div wire:click="$toggle('hasVignette')" class="studio-toggle {{ $hasVignette ? 'active' : '' }}" role="button">
                            <span class="studio-toggle-knob"></span>
                        </div>
                    </div>
                    @if($hasVignette)
                    <div class="vignette-btn-group">
                        <div wire:click="$set('vignetteType', 'black')"
                             class="vignette-btn {{ $vignetteType === 'black' ? 'active-black' : '' }}" role="button">
                            Preta
                        </div>
                        <div wire:click="$set('vignetteType', 'white')"
                             class="vignette-btn {{ $vignetteType === 'white' ? 'active-white' : '' }}" role="button">
                            Branca
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Logo --}}
                <div class="sb-section">
                    <span class="section-title">Marca</span>
                    <div class="flex items-center gap-2">
                        <label class="logo-upload-btn">
                            <x-heroicon-o-arrow-up-tray class="w-3.5 h-3.5 text-zinc-400" style="width: 14px; height: 14px;" />
                            <span class="logo-upload-text">{{ $logoUpload ? 'Trocar Logo' : 'Enviar Logo' }}</span>
                            <input type="file" wire:model="logoUpload" class="hidden" accept="image/*">
                        </label>
                        @if($logoUrl)
                            <div wire:click="$set('logoUrl', null)" class="w-8 h-8 flex items-center justify-center rounded-lg border border-red-200 text-red-400 hover:bg-red-50 transition cursor-pointer" role="button" style="width: 32px; height: 32px; flex-shrink: 0;">
                                <x-heroicon-m-x-mark class="w-3.5 h-3.5" style="width: 14px; height: 14px;"/>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Artes Recentes --}}
                <div class="sb-section" wire:poll.10s>
                    <span class="section-title">Artes Recentes</span>
                    <div class="recent-grid">
                        @foreach($this->recentPosts->where('status', '!=', 'failed') as $p)
                            <div class="art-card relative aspect-square rounded-lg overflow-hidden border border-zinc-100 dark:border-zinc-800 group shadow-sm">
                                @if($p->isGenerated())
                                    <img src="{{ $p->output_url }}" class="w-full h-full object-cover">
                                    <div class="art-overlay">
                                        <a href="{{ $p->output_url }}" target="_blank" class="btn-action-mini" title="Ver">
                                            <x-heroicon-m-eye class="w-3.5 h-3.5" />
                                        </a>
                                        <a href="{{ $p->output_url }}" download="arte-{{ $p->id }}.png" class="btn-action-mini" title="Baixar">
                                            <x-heroicon-m-arrow-down-tray class="w-3.5 h-3.5" />
                                        </a>
                                        <button wire:click="deletePost({{ $p->id }})" class="btn-action-mini btn-delete" title="Excluir">
                                            <x-heroicon-m-trash class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-zinc-50 dark:bg-zinc-900">
                                        <x-heroicon-o-arrow-path class="w-4 h-4 text-amber-500 animate-spin" />
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </aside>

        {{-- Main Area --}}
        <main class="preview-main">
            <div class="canvas-container" id="canvas-wrapper">
                <div class="canvas-box" id="card-container" style="position: relative; overflow: hidden; padding: 0;">
                    
                    {{-- Layer 1: Background --}}
                    <div class="absolute inset-0 z-0 bg-cover bg-center" 
                         style="background-image: url('{{ $this->selectedBackgroundUrl }}');">
                    </div>

                    {{-- Layer 1.5: Overlay & Pattern --}}
                    <div class="canvas-overlay absolute inset-0 z-[5]"
                        style="background-color: {{ $overlayColor }}; opacity: {{ $overlayOpacity / 100 }}; pointer-events: none;"></div>

                    @if($pattern)
                        <div class="canvas-pattern absolute inset-0 z-[6]" style="
                                mask-image: url('/assets/patterns/{{ $pattern }}.png');
                                -webkit-mask-image: url('/assets/patterns/{{ $pattern }}.png');
                                mask-size: {{ $patternSize }}px;
                                -webkit-mask-size: {{ $patternSize }}px;
                                background-color: {{ $patternColor }};
                                opacity: 0.2;
                                pointer-events: none;
                            "></div>
                    @endif

                    {{-- Layer 1.6: Vignette --}}
                    @if($hasVignette)
                        <div class="absolute inset-0 z-[7] pointer-events-none"
                             style="background: radial-gradient(circle, transparent 30%, {{ $vignetteType === 'black' ? 'rgba(0,0,0,0.6)' : 'rgba(255,255,255,0.6)' }} 100%);">
                        </div>
                    @endif


                    {{-- Layer 3: Branding/Logo --}}
                    @if($logoUrl)
                        <div class="absolute z-20" style="top: 5%; right: 5%; width: 120px; height: auto;">
                            <img src="{{ $logoUrl }}" class="w-full h-auto object-contain">
                        </div>
                    @endif

                    {{-- Layer 4: Content/Text --}}
                    <div class="canvas-text absolute z-30 w-full"
                        style="
                            top: {{ $textY }}%;
                            left: {{ $textX }}%;
                            transform: translate(-{{ $textX }}%, -{{ $textY }}%);
                            padding: 0 48px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            text-align: {{ $textAlign }};
                            font-family: '{{ str_replace('+', ' ', $fontFamily) }}', sans-serif !important; 
                            color: {{ $textColor }}; 
                            font-size: {{ $fontSize }}px; 
                            font-weight: {{ $isBold ? '900' : '400' }}; 
                            line-height: 1.1; 
                            text-transform: uppercase;
                            pointer-events: none;
                        ">
                        <div class="w-full break-words">
                            {!! nl2br(e($quote ?: '')) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="command-bar-wrapper">
                <div class="flex flex-col gap-3 items-center">
                    <div class="preset-strip">
                        @foreach(\App\Enums\CardPreset::cases() as $case)
                            <button wire:click="selectPreset('{{ $case->value }}')"
                                class="btn-preset-mini {{ $preset === $case->value ? 'active' : '' }}">
                                {{ $case->label() }}
                            </button>
                        @endforeach
                    </div>
                    
                    <div class="flex gap-2">
                        <button wire:click="generateAiDesign" class="generate-btn h-10 px-6 text-xs" style="background: #8b5cf6; color: white;">
                            <x-heroicon-m-bolt class="w-3.5 h-3.5" />
                            <span>Mágica IA</span>
                        </button>
                        <button onclick="takeSnapshot()" id="generate-trigger" class="generate-btn h-10 px-6 text-xs">
                            <x-heroicon-m-sparkles class="w-3.5 h-3.5" />
                            <span>Gerar Arte</span>
                        </button>
                    </div>
                </div>

                <div class="command-bar !w-[800px] !h-auto min-h-[64px] py-2">
                    <div
                        class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-900 p-1 rounded-full border border-zinc-200 dark:border-zinc-800">
                        <button wire:click="selectPreset('top')"
                            class="w-8 h-8 rounded-full flex items-center justify-center {{ $preset === 'top' ? 'bg-amber-500 text-black' : 'text-zinc-400' }}">
                            <x-heroicon-m-bars-2 class="w-4 h-4 rotate-180" />
                        </button>
                        <button wire:click="selectPreset('bold_center')"
                            class="w-8 h-8 rounded-full flex items-center justify-center {{ $preset === 'bold_center' ? 'bg-amber-500 text-black' : 'text-zinc-400' }}">
                            <x-heroicon-m-pause class="w-4 h-4 rotate-90" />
                        </button>
                        <button wire:click="selectPreset('bottom')"
                            class="w-8 h-8 rounded-full flex items-center justify-center {{ $preset === 'bottom' ? 'bg-amber-500 text-black' : 'text-zinc-400' }}">
                            <x-heroicon-m-bars-2 class="w-4 h-4" />
                        </button>
                    </div>
                    <textarea wire:model.live.debounce.300ms="quote" 
                              class="prompt-input resize-none py-2 max-h-[150px] overflow-y-auto w-full"
                              placeholder="O que você quer expressar hoje?"
                              rows="1"></textarea>
                </div>
            </div>
        </main>

        {{-- Right Sidebar --}}
        <aside class="sidebar-gallery">
            <button wire:click="mountAction('uploadBackground')" class="btn-upload" title="Upload Imagem">
                <x-heroicon-o-plus class="w-6 h-6" />
            </button>
            <div class="w-full h-px bg-zinc-200 dark:bg-zinc-800 my-2"></div>
            @foreach($this->backgrounds as $bg)
                <div class="asset-thumb {{ $backgroundImageId === $bg->id ? 'active' : '' }}"
                    wire:click="selectBackground({{ $bg->id }})">
                    <img src="{{ $bg->getThumbnailUrl() }}" loading="lazy" class="w-full h-full object-cover">
                </div>
            @endforeach
        </aside>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>


        // Auto-expand textarea
        const tx = document.getElementsByTagName("textarea");
        for (let i = 0; i < tx.length; i++) {
            tx[i].setAttribute("style", "height:" + (tx[i].scrollHeight) + "px;overflow-y:hidden;");
            tx[i].addEventListener("input", OnInput, false);
        }

        function OnInput() {
            this.style.height = 0;
            this.style.height = (this.scrollHeight) + "px";
        }

        function takeSnapshot() {
            const el = document.getElementById('card-container');
            const btn = document.getElementById('generate-trigger');
            if (!btn) return;
            const originalContent = btn.innerHTML;
            btn.classList.add('opacity-50', 'pointer-events-none');
            btn.querySelector('span').innerText = 'Capturando...';
            html2canvas(el, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
                logging: false,
                onclone: (clonedDoc) => {
                    const clonedEl = clonedDoc.getElementById('card-container');
                    if (clonedEl) clonedEl.style.transform = 'none';
                }
            }).then(canvas => {
                const dataUrl = canvas.toDataURL('image/png', 1.0);
                @this.saveSnapshot(dataUrl).then(() => {
                    btn.innerHTML = originalContent;
                    btn.classList.remove('opacity-50', 'pointer-events-none');
                });
            }).catch(err => {
                console.error('Snapshot Error:', err);
                btn.innerHTML = originalContent;
                btn.classList.remove('opacity-50', 'pointer-events-none');
            });
        }
    </script>
    <x-filament-actions::modals />
</x-filament-panels::page>