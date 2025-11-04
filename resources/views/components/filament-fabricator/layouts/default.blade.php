@props(['page'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('theme') == 'dark' ? 'dark' : '' }}">

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    @filamentStyles
    @vite('resources/css/app.css')
</head>

<body class="bg-white text-gray-900">
    @livewire('notifications')

    <body class="bg-white text-black">
        @livewire('notifications')

        {{-- Banner de Promoção --}}
        <div class="bg-indigo-100 text-black py-3 border-b border-indigo-200">
            <div class="container mx-auto px-4 text-center">
                <p class="text-sm md:text-base font-medium">
                    🎉 <strong>PROMOÇÃO ESPECIAL:</strong> Desconto de 15% em todos os produtos de concreto usinado!
                    Oferta
                    válida até 31/12/2025.
                    <a href="#contato"
                        class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline ml-2 font-semibold">Saiba
                        mais</a>
                </p>
            </div>
        </div>

        {{-- Header --}}
        <header class="bg-white text-black shadow-sm border-b border-gray-100">
            <div class="container mx-auto px-4 py-8">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-3xl font-bold text-indigo-600">Concreto Usinado Pro</h1>
                        <span class="text-sm text-gray-600">Qualidade e Durabilidade</span>
                    </div>
                    <nav class="hidden md:flex space-x-8">
                        <a href="#inicio"
                            class="text-black hover:text-indigo-600 transition-colors font-medium text-lg">Início</a>
                        <a href="#sobre"
                            class="text-black hover:text-indigo-600 transition-colors font-medium text-lg">Sobre</a>
                        <a href="#servicos"
                            class="text-black hover:text-indigo-600 transition-colors font-medium text-lg">Serviços</a>
                        <a href="#produtos"
                            class="text-black hover:text-indigo-600 transition-colors font-medium text-lg">Produtos</a>
                        <a href="#contato"
                            class="text-black hover:text-indigo-600 transition-colors font-medium text-lg">Contato</a>
                    </nav>
                    <button class="md:hidden text-black hover:text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        {{-- Hero Section --}}
        <section id="inicio" class="bg-white text-black py-24 border-b border-gray-100">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-6xl md:text-7xl font-bold mb-8 text-black">
                    Concreto Usinado de <span class="text-indigo-600">Alta Qualidade</span>
                </h2>
                <p class="text-xl md:text-2xl mb-12 text-gray-700 max-w-4xl mx-auto leading-relaxed">
                    Soluções completas em concreto usinado para construção civil.
                    Durabilidade, resistência e eficiência para seus projetos.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="#contato"
                        class="bg-indigo-600 hover:bg-indigo-700 px-10 py-5 rounded-none font-semibold text-lg transition-colors text-white border border-indigo-600">
                        Solicitar Orçamento
                    </a>
                    <a href="#produtos"
                        class="border-2 border-indigo-600 hover:bg-indigo-600 hover:text-white px-10 py-5 rounded-none font-semibold text-lg transition-colors text-indigo-600">
                        Ver Produtos
                    </a>
                </div>
            </div>
        </section>

        {{-- Sobre a Empresa --}}
        <section id="sobre" class="py-24 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-20">
                    <h3 class="text-5xl font-bold text-black mb-6">Sobre Nossa Empresa</h3>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Há mais de 20 anos no mercado, somos especialistas em concreto usinado,
                        oferecendo soluções inovadoras para a construção civil.
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-12">
                    <div class="bg-white p-10 text-center border border-gray-200">
                        <div class="w-20 h-20 bg-indigo-600 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold text-black mb-4">Tecnologia Avançada</h4>
                        <p class="text-gray-600 leading-relaxed">
                            Utilizamos equipamentos de última geração para garantir a qualidade
                            e consistência de nossos produtos.
                        </p>
                    </div>
                    <div class="bg-white p-10 text-center border border-gray-200">
                        <div class="w-20 h-20 bg-indigo-600 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold text-black mb-4">Qualidade Garantida</h4>
                        <p class="text-gray-600 leading-relaxed">
                            Todos os nossos produtos passam por rigorosos controles de qualidade
                            e atendem às normas técnicas vigentes.
                        </p>
                    </div>
                    <div class="bg-white p-10 text-center border border-gray-200">
                        <div class="w-20 h-20 bg-indigo-600 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold text-black mb-4">Atendimento Personalizado</h4>
                        <p class="text-gray-600 leading-relaxed">
                            Oferecemos consultoria especializada e soluções sob medida
                            para cada projeto dos nossos clientes.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Serviços --}}
        <section id="servicos" class="py-24 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-20">
                    <h3 class="text-5xl font-bold text-black mb-6">Nossos Serviços</h3>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Oferecemos uma gama completa de serviços em concreto usinado
                        para atender todas as necessidades da sua obra.
                    </p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <h4 class="text-2xl font-bold text-black mb-4">Produção de Concreto Usinado</h4>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Fabricação de diversos tipos de concreto usinado com diferentes
                            resistências e características específicas.
                        </p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>• Concreto convencional</li>
                            <li>• Concreto de alta resistência</li>
                            <li>• Concreto leve</li>
                            <li>• Concreto colorido</li>
                        </ul>
                    </div>
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <h4 class="text-2xl font-bold text-black mb-4">Estruturas Pré-Moldadas</h4>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Vigas, pilares, lajes e outros elementos estruturais
                            produzidos em nossa fábrica.
                        </p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>• Vigas de concreto</li>
                            <li>• Pilares estruturais</li>
                            <li>• Lajes pré-moldadas</li>
                            <li>• Fundações</li>
                        </ul>
                    </div>
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <h4 class="text-2xl font-bold text-black mb-4">Consultoria Técnica</h4>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Assessoria especializada para projetos de construção,
                            dimensionamento e especificações técnicas.
                        </p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>• Dimensionamento estrutural</li>
                            <li>• Análise de viabilidade</li>
                            <li>• Especificações técnicas</li>
                            <li>• Acompanhamento de obra</li>
                        </ul>
                    </div>
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <h4 class="text-2xl font-bold text-black mb-4">Transporte e Montagem</h4>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Serviço completo de transporte até a obra e montagem
                            dos elementos pré-moldados.
                        </p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>• Transporte especializado</li>
                            <li>• Montagem no local</li>
                            <li>• Guinchos e equipamentos</li>
                            <li>• Equipe treinada</li>
                        </ul>
                    </div>
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <h4 class="text-2xl font-bold text-black mb-4">Manutenção e Reforma</h4>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Serviços de manutenção e reforma de estruturas de concreto
                            existentes.
                        </p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>• Reparos estruturais</li>
                            <li>• Reforço de estruturas</li>
                            <li>• Impermeabilização</li>
                            <li>• Recuperação de concreto</li>
                        </ul>
                    </div>
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors">
                        <h4 class="text-2xl font-bold text-black mb-4">Projetos Personalizados</h4>
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Desenvolvimento de soluções personalizadas para projetos
                            especiais e necessidades específicas.
                        </p>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>• Projetos especiais</li>
                            <li>• Soluções customizadas</li>
                            <li>• Inovação tecnológica</li>
                            <li>• Pesquisa e desenvolvimento</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- Produtos em Destaque --}}
        <section id="produtos" class="py-24 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-20">
                    <h3 class="text-5xl font-bold text-black mb-6">Produtos em Destaque</h3>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Conheça nossa linha completa de produtos de concreto usinado,
                        desenvolvidos com tecnologia de ponta.
                    </p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors text-center">
                        <div class="w-16 h-16 bg-indigo-600 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-black mb-3">Blocos de Concreto</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Blocos estruturais de alta resistência para construção civil.
                        </p>
                    </div>
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors text-center">
                        <div class="w-16 h-16 bg-indigo-600 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-black mb-3">Tubos de Concreto</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Tubos para drenagem, esgoto e infraestrutura urbana.
                        </p>
                    </div>
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors text-center">
                        <div class="w-16 h-16 bg-indigo-600 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-black mb-3">Meios-Fios</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Elementos para delimitação de vias e calçadas.
                        </p>
                    </div>
                    <div class="p-8 border border-gray-200 hover:border-indigo-300 transition-colors text-center">
                        <div class="w-16 h-16 bg-indigo-600 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-black mb-3">Lajes Pré-Moldadas</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Lajes estruturais para coberturas e pavimentos.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Call to Action --}}
        <section class="py-24 bg-indigo-600 text-white">
            <div class="container mx-auto px-4 text-center">
                <h3 class="text-5xl font-bold mb-6">Pronto para Começar seu Projeto?</h3>
                <p class="text-xl mb-12 text-indigo-100 max-w-3xl mx-auto leading-relaxed">
                    Entre em contato conosco hoje mesmo e receba uma consultoria gratuita
                    para seu projeto de construção.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="tel:+5511999999999"
                        class="bg-white hover:bg-gray-100 px-10 py-5 font-semibold text-lg transition-colors text-indigo-600 border border-white">
                        📞 Ligar Agora
                    </a>
                    <a href="https://wa.me/5511999999999"
                        class="bg-white hover:bg-gray-100 px-10 py-5 font-semibold text-lg transition-colors text-indigo-600 border border-white">
                        💬 WhatsApp
                    </a>
                </div>
            </div>
        </section>

        {{-- Contato --}}
        <section id="contato" class="py-24 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-20">
                    <h3 class="text-5xl font-bold text-black mb-6">Entre em Contato</h3>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Estamos prontos para atender suas necessidades.
                        Entre em contato e solicite seu orçamento.
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-16">
                    <div>
                        <h4 class="text-3xl font-bold text-black mb-8">Informações de Contato</h4>
                        <div class="space-y-6">
                            <div class="flex items-center">
                                <svg class="w-7 h-7 text-indigo-600 mr-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-gray-700 text-lg">Rua do Concreto, 123 - São Paulo, SP</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-7 h-7 text-indigo-600 mr-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                                <span class="text-gray-700 text-lg">(11) 9999-9999</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-7 h-7 text-indigo-600 mr-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="text-gray-700 text-lg">contato@concretousinado.com.br</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-7 h-7 text-indigo-600 mr-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-gray-700 text-lg">Seg-Sex: 7h às 17h | Sáb: 8h às 12h</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-3xl font-bold text-black mb-8">Envie sua Mensagem</h4>
                        <form class="space-y-6">
                            <div>
                                <input type="text" placeholder="Seu Nome"
                                    class="w-full px-6 py-4 border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent text-lg">
                            </div>
                            <div>
                                <input type="email" placeholder="Seu E-mail"
                                    class="w-full px-6 py-4 border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent text-lg">
                            </div>
                            <div>
                                <input type="tel" placeholder="Seu Telefone"
                                    class="w-full px-6 py-4 border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent text-lg">
                            </div>
                            <div>
                                <textarea rows="5" placeholder="Sua Mensagem"
                                    class="w-full px-6 py-4 border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent text-lg"></textarea>
                            </div>
                            <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 font-semibold transition-colors text-lg">
                                Enviar Mensagem
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section> {{-- Footer --}}
        <footer class="bg-indigo-900 text-white py-16">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-12">
                    <div>
                        <h5 class="text-2xl font-bold text-indigo-400 mb-6">Concreto Usinado Pro</h5>
                        <p class="text-gray-300 text-base leading-relaxed">
                            Há mais de 20 anos produzindo soluções em concreto usinado
                            com qualidade e compromisso com nossos clientes.
                        </p>
                    </div>
                    <div>
                        <h6 class="text-xl font-semibold mb-6">Links Rápidos</h6>
                        <ul class="space-y-3 text-base text-gray-300">
                            <li><a href="#inicio" class="text-white hover:text-indigo-300 transition-colors">Início</a>
                            </li>
                            <li><a href="#sobre" class="text-white hover:text-indigo-300 transition-colors">Sobre</a>
                            </li>
                            <li><a href="#servicos"
                                    class="text-white hover:text-indigo-300 transition-colors">Serviços</a></li>
                            <li><a href="#produtos"
                                    class="text-white hover:text-indigo-300 transition-colors">Produtos</a></li>
                            <li><a href="#contato"
                                    class="text-white hover:text-indigo-300 transition-colors">Contato</a></li>
                        </ul>
                    </div>
                    <div>
                        <h6 class="text-xl font-semibold mb-6">Serviços</h6>
                        <ul class="space-y-3 text-base text-gray-300">
                            <li>Produção de Concreto</li>
                            <li>Estruturas Pré-Moldadas</li>
                            <li>Consultoria Técnica</li>
                            <li>Transporte e Montagem</li>
                            <li>Manutenção e Reforma</li>
                        </ul>
                    </div>
                    <div>
                        <h6 class="text-xl font-semibold mb-6">Contato</h6>
                        <ul class="space-y-3 text-base text-gray-300">
                            <li>📞 (11) 9999-9999</li>
                            <li>📧 contato@concretousinado.com.br</li>
                            <li>📍 São Paulo, SP</li>
                            <li>🕒 Seg-Sex: 7h-17h</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-12 pt-8 text-center">
                    <p class="text-gray-400 text-base">
                        © 2025 Concreto Usinado Pro. Todos os direitos reservados.
                        Desenvolvido com ❤️ para a construção civil.
                    </p>
                </div>
            </div>
        </footer>

        @filamentScripts
        @vite('resources/js/app.js')
    </body>

</html>
