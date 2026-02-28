<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="theme-color" content="#e5b800">
    <meta name="description" content="Concreto usinado de alta qualidade e locacao de equipamentos para sua obra. Entrega rapida, laboratorio proprio e suporte tecnico especializado.">
    <title>ConcretoPro | Concreto Usinado & Equipamentos</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['Space Grotesk', 'monospace'],
                    },
                    colors: {
                        background: 'oklch(0.97 0.002 250)',
                        foreground: 'oklch(0.18 0.01 250)',
                        card: {
                            DEFAULT: 'oklch(1 0 0)',
                            foreground: 'oklch(0.18 0.01 250)',
                        },
                        primary: {
                            DEFAULT: 'oklch(0.82 0.17 85)',
                            foreground: 'oklch(0.18 0.01 250)',
                        },
                        muted: {
                            DEFAULT: 'oklch(0.92 0.005 250)',
                            foreground: 'oklch(0.45 0.01 250)',
                        },
                        border: 'oklch(0.88 0.005 250)',
                        input: 'oklch(0.88 0.005 250)',
                    },
                },
            },
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="font-sans antialiased bg-background text-foreground">

<!-- ========== HEADER ========== -->
<header class="fixed top-0 left-0 right-0 z-50 bg-card/90 backdrop-blur-md border-b border-border" id="site-header">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 lg:px-8">
        <a href="#" class="flex items-center gap-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-md bg-primary">
                <i data-lucide="truck" class="h-5 w-5 text-primary-foreground"></i>
            </div>
            <span class="font-mono text-xl font-bold tracking-tight text-foreground">ConcretoPro</span>
        </a>

        <nav class="hidden items-center gap-8 md:flex">
            <a href="#servicos" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Servicos</a>
            <a href="#calculadora" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Calculadora</a>
            <a href="#orcamento" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Orcamento</a>
            <a href="#diferenciais" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">Diferenciais</a>
        </nav>

        <a href="#orcamento" class="hidden rounded-md bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90 md:inline-flex">
            Solicitar Orcamento
        </a>

        <button onclick="toggleMobileMenu()" class="inline-flex items-center justify-center rounded-md p-2 text-foreground md:hidden" aria-label="Abrir menu" id="mobile-menu-btn">
            <i data-lucide="menu" class="h-6 w-6" id="menu-icon-open"></i>
            <i data-lucide="x" class="h-6 w-6 hidden" id="menu-icon-close"></i>
        </button>
    </div>

    <div class="hidden border-t border-border bg-card px-4 pb-4 md:hidden" id="mobile-menu">
        <nav class="flex flex-col gap-3 pt-3">
            <a href="#servicos" onclick="closeMobileMenu()" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">Servicos</a>
            <a href="#calculadora" onclick="closeMobileMenu()" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">Calculadora</a>
            <a href="#orcamento" onclick="closeMobileMenu()" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">Orcamento</a>
            <a href="#diferenciais" onclick="closeMobileMenu()" class="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">Diferenciais</a>
            <a href="#orcamento" onclick="closeMobileMenu()" class="mt-2 rounded-md bg-primary px-5 py-2.5 text-center text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90">Solicitar Orcamento</a>
        </nav>
    </div>
</header>

<main>

<!-- ========== HERO ========== -->
<section class="relative flex min-h-[90vh] items-center overflow-hidden bg-foreground pt-16">
    <div class="pointer-events-none absolute inset-0 opacity-5">
        <div class="absolute left-1/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-2/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-3/4 top-0 h-full w-px bg-background"></div>
        <div class="absolute left-0 top-1/3 h-px w-full bg-background"></div>
        <div class="absolute left-0 top-2/3 h-px w-full bg-background"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 py-20 lg:px-8">
        <div class="flex flex-col items-start gap-8 lg:flex-row lg:items-center lg:gap-16">
            <div class="flex-1">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-4 py-1.5">
                    <span class="h-2 w-2 rounded-full bg-primary"></span>
                    <span class="text-xs font-medium uppercase tracking-wider text-primary">Qualidade Certificada</span>
                </div>

                <h1 class="font-mono text-4xl font-bold leading-tight tracking-tight text-background md:text-5xl lg:text-6xl" style="text-wrap: balance;">
                    Concreto usinado com <span class="text-primary">agilidade</span> e precisao no traco
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-relaxed text-background/60">
                    Entrega rapida, rastreamento em tempo real e suporte tecnico especializado para garantir o sucesso da sua obra do inicio ao fim.
                </p>

                <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                    <a href="#orcamento" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-8 py-4 text-base font-semibold text-primary-foreground transition-all hover:bg-primary/90 hover:gap-3">
                        Solicitar Orcamento Agora
                        <i data-lucide="arrow-right" class="h-5 w-5"></i>
                    </a>
                    <a href="#calculadora" class="inline-flex items-center justify-center gap-2 rounded-md border border-background/20 px-8 py-4 text-base font-medium text-background transition-colors hover:bg-background/10">
                        Calcular Volume
                    </a>
                </div>
            </div>

            <div class="flex-1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-4">
                        <div class="rounded-lg border border-primary/20 bg-primary/10 p-6">
                            <p class="font-mono text-4xl font-bold text-primary">500+</p>
                            <p class="mt-1 text-sm text-background/60">Obras atendidas</p>
                        </div>
                        <div class="rounded-lg border border-background/10 bg-background/5 p-6">
                            <p class="font-mono text-4xl font-bold text-background">98%</p>
                            <p class="mt-1 text-sm text-background/60">Pontualidade</p>
                        </div>
                    </div>
                    <div class="mt-8 flex flex-col gap-4">
                        <div class="rounded-lg border border-background/10 bg-background/5 p-6">
                            <p class="font-mono text-4xl font-bold text-background">24h</p>
                            <p class="mt-1 text-sm text-background/60">Suporte tecnico</p>
                        </div>
                        <div class="rounded-lg border border-primary/20 bg-primary/10 p-6">
                            <p class="font-mono text-4xl font-bold text-primary">15+</p>
                            <p class="mt-1 text-sm text-background/60">Anos de experiencia</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== SERVICES ========== -->
<section id="servicos" class="bg-background py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="mb-16 max-w-2xl">
            <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">Nossos Servicos</span>
            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">Tudo que sua obra precisa em um so lugar</h2>
            <p class="mt-4 text-lg leading-relaxed text-muted-foreground">Da fundacao ao acabamento, oferecemos solucoes completas com qualidade garantida.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Concreto Usinado -->
            <div class="group flex flex-col rounded-lg border border-border bg-card p-8 transition-all hover:border-primary/40 hover:shadow-lg">
                <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-lg bg-primary/10">
                    <i data-lucide="droplets" class="h-7 w-7 text-primary"></i>
                </div>
                <h3 class="font-mono text-xl font-bold text-card-foreground">Concreto Usinado</h3>
                <span class="mt-1 text-xs font-medium uppercase tracking-wider text-muted-foreground">por m&sup3;</span>
                <p class="mt-4 flex-1 text-sm leading-relaxed text-muted-foreground">Concreto de alta qualidade com controle rigoroso de traco e resistencia. Todos os tipos de FCK disponiveis para sua obra.</p>
                <ul class="mt-6 flex flex-col gap-2 border-t border-border pt-6">
                    <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>FCK 20 a 50 MPa</li>
                    <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>Traco personalizado</li>
                    <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>Nota fiscal e certificado</li>
                </ul>
                <a href="#orcamento" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-primary transition-all group-hover:gap-3">
                    Solicitar Orcamento <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>

            <!-- Bombeamento -->
            <div class="group flex flex-col rounded-lg border border-border bg-card p-8 transition-all hover:border-primary/40 hover:shadow-lg">
                <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-lg bg-primary/10">
                    <i data-lucide="gauge" class="h-7 w-7 text-primary"></i>
                </div>
                <h3 class="font-mono text-xl font-bold text-card-foreground">Bombeamento de Concreto</h3>
                <span class="mt-1 text-xs font-medium uppercase tracking-wider text-muted-foreground">por servico</span>
                <p class="mt-4 flex-1 text-sm leading-relaxed text-muted-foreground">Servico de bombeamento para lajes, fundacoes e estruturas em altura. Equipamentos modernos e operadores certificados.</p>
                <ul class="mt-6 flex flex-col gap-2 border-t border-border pt-6">
                    <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>Bomba estacionaria</li>
                    <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>Bomba lanca</li>
                    <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>Ate 40m de altura</li>
                </ul>
                <a href="#orcamento" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-primary transition-all group-hover:gap-3">
                    Solicitar Orcamento <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>

            <!-- Locacao -->
            <div class="group flex flex-col rounded-lg border border-border bg-card p-8 transition-all hover:border-primary/40 hover:shadow-lg">
                <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-lg bg-primary/10">
                    <i data-lucide="wrench" class="h-7 w-7 text-primary"></i>
                </div>
                <h3 class="font-mono text-xl font-bold text-card-foreground">Locacao de Maquinas</h3>
                <span class="mt-1 text-xs font-medium uppercase tracking-wider text-muted-foreground">diaria / hora</span>
                <p class="mt-4 flex-1 text-sm leading-relaxed text-muted-foreground">Equipamentos de ponta para sua obra. Retroescavadeiras, minicarregadeiras, compactadores e muito mais.</p>
                <ul class="mt-6 flex flex-col gap-2 border-t border-border pt-6">
                    <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>Retroescavadeira</li>
                    <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>Minicarregadeira</li>
                    <li class="flex items-center gap-2 text-sm text-card-foreground"><span class="h-1.5 w-1.5 rounded-full bg-primary"></span>Compactador de solo</li>
                </ul>
                <a href="#orcamento" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-primary transition-all group-hover:gap-3">
                    Solicitar Orcamento <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ========== VOLUME CALCULATOR ========== -->
<section id="calculadora" class="bg-foreground py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div>
                <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">Ferramenta Interativa</span>
                <h2 class="font-mono text-3xl font-bold tracking-tight text-background md:text-4xl" style="text-wrap: balance;">Calculadora de Volume de Concreto</h2>
                <p class="mt-4 text-lg leading-relaxed text-background/60">Calcule rapidamente o volume de concreto necessario para sua obra. Insira as dimensoes e obtenha o resultado em metros cubicos instantaneamente.</p>
                <div class="mt-8 flex flex-col gap-3 text-sm text-background/40">
                    <p>Formula: Volume = Largura &times; Comprimento &times; Espessura</p>
                    <p>Recomendamos adicionar 10% de margem de seguranca ao resultado.</p>
                </div>
            </div>

            <div class="rounded-lg border border-background/10 bg-background/5 p-8">
                <div class="mb-8 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                        <i data-lucide="calculator" class="h-5 w-5 text-primary"></i>
                    </div>
                    <h3 class="font-mono text-lg font-bold text-background">Calcule Agora</h3>
                </div>

                <div class="flex flex-col gap-5">
                    <div>
                        <label for="calc-largura" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-background/60">Largura (metros)</label>
                        <input id="calc-largura" type="number" step="0.01" min="0" placeholder="Ex: 5.00" oninput="calcularVolume()"
                            class="w-full rounded-md border border-background/10 bg-background/5 px-4 py-3 text-background placeholder:text-background/30 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label for="calc-comprimento" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-background/60">Comprimento (metros)</label>
                        <input id="calc-comprimento" type="number" step="0.01" min="0" placeholder="Ex: 10.00" oninput="calcularVolume()"
                            class="w-full rounded-md border border-background/10 bg-background/5 px-4 py-3 text-background placeholder:text-background/30 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label for="calc-espessura" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-background/60">Espessura (metros)</label>
                        <input id="calc-espessura" type="number" step="0.01" min="0" placeholder="Ex: 0.15" oninput="calcularVolume()"
                            class="w-full rounded-md border border-background/10 bg-background/5 px-4 py-3 text-background placeholder:text-background/30 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>
                </div>

                <div class="mt-8 rounded-lg border border-primary/30 bg-primary/10 p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-primary/80">Volume Total</p>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-mono text-4xl font-bold text-primary" id="calc-volume">0.00</span>
                        <span class="text-lg font-medium text-primary/70">m&sup3;</span>
                    </div>
                    <p class="mt-2 text-xs text-background/40 hidden" id="calc-margem">
                        Com margem de 10%: <span class="font-semibold text-background/60" id="calc-volume-margem"></span>
                    </p>
                </div>

                <button onclick="limparCalculadora()" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-md border border-background/10 px-4 py-3 text-sm font-medium text-background/60 transition-colors hover:bg-background/5 hover:text-background">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    Limpar Campos
                </button>
            </div>
        </div>
    </div>
</section>

<!-- ========== QUOTE WIDGET ========== -->
<section id="orcamento" class="bg-muted py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <!-- Success state -->
        <div class="hidden" id="quote-success">
            <div class="mx-auto max-w-2xl px-4 text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-primary/10">
                    <i data-lucide="check-circle" class="h-10 w-10 text-primary"></i>
                </div>
                <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground">Orcamento Enviado!</h2>
                <p class="mt-4 text-lg leading-relaxed text-muted-foreground">Recebemos sua solicitacao com sucesso. Nossa equipe entrara em contato em ate 2 horas uteis.</p>
                <button onclick="resetQuoteForm()" class="mt-8 inline-flex items-center gap-2 rounded-md bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90">Enviar Novo Orcamento</button>
            </div>
        </div>

        <!-- Form state -->
        <div id="quote-form-wrapper">
            <div class="grid gap-12 lg:grid-cols-5 lg:gap-16">
                <div class="lg:col-span-2">
                    <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">Orcamento Personalizado</span>
                    <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">Solicite seu orcamento em minutos</h2>
                    <p class="mt-4 text-lg leading-relaxed text-muted-foreground">Preencha o formulario ao lado e receba um orcamento detalhado e sem compromisso.</p>

                    <div class="mt-10 flex flex-col gap-6">
                        <div class="flex items-start gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 font-mono text-sm font-bold text-primary">1</span>
                            <div>
                                <p class="font-semibold text-foreground">Escolha o servico</p>
                                <p class="text-sm text-muted-foreground">Selecione concreto, bombeamento ou locacao.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 font-mono text-sm font-bold text-primary">2</span>
                            <div>
                                <p class="font-semibold text-foreground">Preencha os detalhes</p>
                                <p class="text-sm text-muted-foreground">Informe volume, tipo de traco ou equipamento desejado.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 font-mono text-sm font-bold text-primary">3</span>
                            <div>
                                <p class="font-semibold text-foreground">Receba o orcamento</p>
                                <p class="text-sm text-muted-foreground">Nossa equipe retornara em ate 2 horas uteis.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-border bg-card p-8 lg:col-span-3">
                    <form id="quote-form" onsubmit="handleQuoteSubmit(event)" class="flex flex-col gap-6">
                        <!-- Service type toggle -->
                        <div>
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Tipo de Servico</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" onclick="setServiceType('concreto')" id="svc-btn-concreto"
                                    class="rounded-md px-4 py-2.5 text-sm font-medium transition-all bg-primary text-primary-foreground">Concreto</button>
                                <button type="button" onclick="setServiceType('bombeamento')" id="svc-btn-bombeamento"
                                    class="rounded-md px-4 py-2.5 text-sm font-medium transition-all border border-border bg-muted text-muted-foreground hover:border-primary/40">Bombeamento</button>
                                <button type="button" onclick="setServiceType('locacao')" id="svc-btn-locacao"
                                    class="rounded-md px-4 py-2.5 text-sm font-medium transition-all border border-border bg-muted text-muted-foreground hover:border-primary/40">Locacao</button>
                            </div>
                        </div>

                        <!-- Common fields -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="q-nome" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Nome Completo</label>
                                <input id="q-nome" name="nome" type="text" required placeholder="Seu nome"
                                    class="w-full rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            </div>
                            <div>
                                <label for="q-telefone" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Telefone / WhatsApp</label>
                                <input id="q-telefone" name="telefone" type="tel" required placeholder="(00) 00000-0000"
                                    class="w-full rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            </div>
                        </div>

                        <div>
                            <label for="q-email" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">E-mail</label>
                            <input id="q-email" name="email" type="email" required placeholder="seu@email.com"
                                class="w-full rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>

                        <!-- Concreto / Bombeamento fields -->
                        <div id="fields-concreto" class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="q-volume" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Volume (m&sup3;)</label>
                                <input id="q-volume" name="volume" type="number" step="0.5" min="0" placeholder="Ex: 15"
                                    class="w-full rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            </div>
                            <div>
                                <label for="q-traco" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Traco / FCK</label>
                                <select id="q-traco" name="traco"
                                    class="w-full rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                                    <option value="FCK 20 MPa">FCK 20 MPa</option>
                                    <option value="FCK 25 MPa">FCK 25 MPa</option>
                                    <option value="FCK 30 MPa">FCK 30 MPa</option>
                                    <option value="FCK 35 MPa">FCK 35 MPa</option>
                                    <option value="FCK 40 MPa">FCK 40 MPa</option>
                                    <option value="FCK 50 MPa">FCK 50 MPa</option>
                                </select>
                            </div>
                        </div>

                        <!-- Locacao fields -->
                        <div id="fields-locacao" class="hidden flex flex-col gap-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="q-maquina" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Equipamento</label>
                                    <select id="q-maquina" name="maquina"
                                        class="w-full rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                                        <option>Retroescavadeira</option>
                                        <option>Minicarregadeira</option>
                                        <option>Compactador de Solo</option>
                                        <option>Rolo Compactador</option>
                                        <option>Betoneira 400L</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="q-quantidade" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Quantidade</label>
                                    <input id="q-quantidade" name="quantidade" type="number" min="1" value="1" placeholder="1"
                                        class="w-full rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Unidade de Locacao</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" onclick="setUnidade('day')" id="unidade-btn-day"
                                        class="rounded-md px-4 py-2.5 text-sm font-medium transition-all bg-primary text-primary-foreground">Diaria</button>
                                    <button type="button" onclick="setUnidade('hour')" id="unidade-btn-hour"
                                        class="rounded-md px-4 py-2.5 text-sm font-medium transition-all border border-border bg-muted text-muted-foreground hover:border-primary/40">Hora</button>
                                </div>
                            </div>
                        </div>

                        <!-- Endereco -->
                        <div>
                            <label for="q-endereco" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Endereco da Obra</label>
                            <input id="q-endereco" name="endereco" type="text" required placeholder="Rua, numero, bairro, cidade"
                                class="w-full rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        </div>

                        <!-- Observacoes -->
                        <div>
                            <label for="q-observacoes" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Observacoes (Opcional)</label>
                            <textarea id="q-observacoes" name="observacoes" rows="3" placeholder="Informacoes adicionais sobre a obra..."
                                class="w-full resize-none rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                        </div>

                        <!-- Submit -->
                        <button type="submit" id="quote-submit-btn"
                            class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-6 py-4 text-base font-semibold text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-70">
                            <i data-lucide="send" class="h-5 w-5" id="submit-icon-send"></i>
                            <i data-lucide="loader-2" class="h-5 w-5 animate-spin hidden" id="submit-icon-loading"></i>
                            <span id="submit-text">Enviar Solicitacao</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== DIFFERENTIALS ========== -->
<section id="diferenciais" class="bg-background py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="mb-16 text-center">
            <span class="mb-4 inline-block text-xs font-semibold uppercase tracking-widest text-primary">Por Que Nos Escolher</span>
            <h2 class="font-mono text-3xl font-bold tracking-tight text-foreground md:text-4xl" style="text-wrap: balance;">Diferenciais que fazem a diferenca na sua obra</h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-muted-foreground">Mais de 15 anos entregando confianca, qualidade e resultado para construtoras e obras em toda a regiao.</p>
        </div>

        <div class="grid gap-8 md:grid-cols-3">
            <div class="flex flex-col items-center text-center">
                <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10">
                    <i data-lucide="zap" class="h-10 w-10 text-primary"></i>
                </div>
                <h3 class="font-mono text-xl font-bold text-foreground">Entrega Rapida</h3>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-muted-foreground">Frota propria com rastreamento em tempo real. Entregamos concreto usinado em ate 2 horas apos a confirmacao do pedido.</p>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10">
                    <i data-lucide="flask-conical" class="h-10 w-10 text-primary"></i>
                </div>
                <h3 class="font-mono text-xl font-bold text-foreground">Laboratorio Proprio</h3>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-muted-foreground">Controle de qualidade rigoroso com ensaios de resistencia e slump test realizados em nosso laboratorio interno.</p>
            </div>

            <div class="flex flex-col items-center text-center">
                <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10">
                    <i data-lucide="headset" class="h-10 w-10 text-primary"></i>
                </div>
                <h3 class="font-mono text-xl font-bold text-foreground">Suporte Tecnico</h3>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-muted-foreground">Equipe de engenheiros disponivel 24h para orientar sobre o traco ideal, volume necessario e melhores praticas de concretagem.</p>
            </div>
        </div>
    </div>
</section>

</main>

<!-- ========== FOOTER ========== -->
<footer class="border-t border-border bg-foreground">
    <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <a href="#" class="flex items-center gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-md bg-primary">
                        <i data-lucide="truck" class="h-5 w-5 text-primary-foreground"></i>
                    </div>
                    <span class="font-mono text-xl font-bold tracking-tight text-background">ConcretoPro</span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-background/50">Excelencia em concreto usinado e locacao de equipamentos. Sua obra merece o melhor desde a fundacao.</p>
            </div>

            <div>
                <h4 class="mb-4 font-mono text-sm font-bold uppercase tracking-wider text-background">Servicos</h4>
                <ul class="flex flex-col gap-2 text-sm text-background/50">
                    <li><a href="#servicos" class="transition-colors hover:text-primary">Concreto Usinado</a></li>
                    <li><a href="#servicos" class="transition-colors hover:text-primary">Bombeamento</a></li>
                    <li><a href="#servicos" class="transition-colors hover:text-primary">Locacao de Maquinas</a></li>
                    <li><a href="#calculadora" class="transition-colors hover:text-primary">Calculadora de Volume</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-4 font-mono text-sm font-bold uppercase tracking-wider text-background">Contato</h4>
                <ul class="flex flex-col gap-3 text-sm text-background/50">
                    <li class="flex items-center gap-2"><i data-lucide="phone" class="h-4 w-4 text-primary"></i>(11) 99999-9999</li>
                    <li class="flex items-center gap-2"><i data-lucide="mail" class="h-4 w-4 text-primary"></i>contato@concretopro.com.br</li>
                    <li class="flex items-start gap-2"><i data-lucide="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-primary"></i>Av. Industrial, 1500 - Distrito Industrial, SP</li>
                </ul>
            </div>
        </div>

        <div class="mt-12 border-t border-background/10 pt-8 text-center text-xs text-background/30">
            ConcretoPro &copy; 2026. Todos os direitos reservados.
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="js/app.js"></script>
<script>lucide.createIcons();</script>
</body>
</html>
