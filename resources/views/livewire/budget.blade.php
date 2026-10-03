<div class="relative max-w-7xl mx-auto text-scheme-light">
    @php
        $isPrimaryBg = str_contains($bgColor ?? '', 'bg-primary');
        $primaryColor = $isPrimaryBg ? '[color-mix(in_srgb,var(--primary),black_85%)]' : 'primary';
        $accentText = $isPrimaryBg ? 'text-[color-mix(in_srgb,var(--primary),black_85%)]' : 'text-primary';
        $accentBg = $isPrimaryBg ? 'bg-[color-mix(in_srgb,var(--primary),black_85%)] text-white' : 'bg-primary text-primary-foreground';
        $accentLightBg = $isPrimaryBg ? 'bg-black/10' : 'bg-primary/5';
        $accentLightBorder = $isPrimaryBg ? 'border-black/20' : 'border-primary/20';
        $boxBg = $isPrimaryBg ? 'bg-black/10' : 'bg-white';
        $boxBorder = $isPrimaryBg ? 'border-black/20' : 'border-border/40';
    @endphp

    @if($isSubmitted)
        <div class="rounded-[2rem] border {{ $boxBorder }} {{ $boxBg }} p-12 text-center shadow-xl shadow-black/5 max-w-2xl mx-auto">
            <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full {{ $accentBg }} shadow-lg shadow-primary/20">
                <i data-lucide="check" class="h-12 w-12"></i>
            </div>
            <h3 class="font-sans text-3xl font-extrabold tracking-tight text-foreground">Orçamento Solicitado!</h3>
            <p class="mt-4 text-lg text-muted-foreground leading-relaxed">
                Nossa equipe recebeu sua solicitação. Em até 24 horas entraremos em contato via WhatsApp ou E-mail para enviar sua proposta personalizada.
            </p>
            <div class="mt-10">
                <button wire:click="resetForm" class="rounded-xl px-8 py-4 text-sm font-bold tracking-widest text-white transition-all hover:-translate-y-1 hover:shadow-lg {{ $accentBg }}">
                    Fazer outro pedido
                </button>
            </div>
        </div>
    @else
        <div class="grid gap-8 lg:grid-cols-12 items-start">
            
            <!-- Esquerda: Formulário -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                
                <!-- Wizard Tabs -->
                <div class="flex items-center justify-between rounded-3xl border {{ $boxBorder }} {{ $boxBg }} p-2 shadow-sm relative overflow-hidden">
                    <div class="absolute inset-y-0 left-0 w-1/2 bg-gradient-to-r from-primary/5 to-transparent pointer-events-none"></div>
                    
                    <button wire:click="setStep(1)" class="relative z-10 flex flex-1 flex-col items-center gap-2 rounded-2xl p-4 transition-all {{ $currentStep === 1 ? 'bg-white shadow-md' : 'hover:bg-muted/50 opacity-60' }}">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold {{ $currentStep >= 1 ? $accentBg : 'bg-muted text-muted-foreground' }}">1</div>
                        <div class="text-center">
                            <strong class="block text-xs font-bold text-foreground">Seu pedido</strong>
                            <span class="hidden md:block text-[10px] text-muted-foreground">O que você precisa?</span>
                        </div>
                    </button>
                    
                    <div class="h-px w-8 bg-border"></div>
                    
                    <button wire:click="setStep(2)" class="relative z-10 flex flex-1 flex-col items-center gap-2 rounded-2xl p-4 transition-all {{ $currentStep === 2 ? 'bg-white shadow-md' : 'hover:bg-muted/50 opacity-60' }}">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold {{ $currentStep >= 2 ? $accentBg : 'bg-muted text-muted-foreground' }}">2</div>
                        <div class="text-center">
                            <strong class="block text-xs font-bold text-foreground">Obra e contato</strong>
                            <span class="hidden md:block text-[10px] text-muted-foreground">Local e seus dados</span>
                        </div>
                    </button>
                    
                    <div class="h-px w-8 bg-border"></div>
                    
                    <button wire:click="setStep(3)" class="relative z-10 flex flex-1 flex-col items-center gap-2 rounded-2xl p-4 transition-all {{ $currentStep === 3 ? 'bg-white shadow-md' : 'hover:bg-muted/50 opacity-60' }}">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold {{ $currentStep >= 3 ? $accentBg : 'bg-muted text-muted-foreground' }}">3</div>
                        <div class="text-center">
                            <strong class="block text-xs font-bold text-foreground">Revisão</strong>
                            <span class="hidden md:block text-[10px] text-muted-foreground">Confira e envie</span>
                        </div>
                    </button>
                </div>

                <!-- Conteúdo do Formulário -->
                <div class="rounded-3xl border {{ $boxBorder }} {{ $boxBg }} p-6 md:p-10 shadow-sm">
                    
                    @if ($currentStep === 1)
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold tracking-tight text-foreground">Selecione os produtos</h3>
                            <p class="text-sm text-muted-foreground mt-1">Escolha os produtos e serviços que sua obra precisa. Você pode adicionar mais de um item.</p>
                        </div>
                        
                        <div class="flex flex-col gap-6">
                            @foreach ($items as $index => $item)
                                <div class="rounded-2xl border border-border/60 bg-muted/20 p-5 relative group transition-all hover:border-primary/30">
                                    <div class="mb-5 flex items-center justify-between border-b border-border/50 pb-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white shadow-sm border border-border/40 text-muted-foreground group-hover:text-primary transition-colors">
                                                <i data-lucide="package" class="h-4 w-4"></i>
                                            </div>
                                            <span class="text-sm font-bold text-foreground">Item {{ $index + 1 }}</span>
                                        </div>
                                        @if(count($items) > 1)
                                            <button type="button" wire:click="removeItem({{ $index }})" class="text-muted-foreground hover:text-red-500 transition-colors p-1" title="Remover item">
                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <div class="grid gap-5 md:grid-cols-12 items-start">
                                        <div class="md:col-span-5">
                                            <label class="mb-1.5 block text-xs font-bold text-foreground">Produto <span class="text-red-500">*</span></label>
                                            <select wire:model.live="items.{{ $index }}.product_id" class="w-full rounded-xl border border-border/50 bg-white px-4 py-3 text-sm font-medium shadow-sm transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                                                <option value="">Selecione...</option>
                                                @foreach($allProducts as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @error("items.{$index}.product_id") <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        
                                        <div class="md:col-span-5">
                                            <label class="mb-1.5 block text-xs font-bold text-foreground">Opção / Traço <span class="text-red-500">*</span></label>
                                            <select wire:model.live="items.{{ $index }}.option_id" class="w-full rounded-xl border border-border/50 bg-white px-4 py-3 text-sm font-medium shadow-sm transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" {{ empty($item['product_id']) ? 'disabled' : '' }}>
                                                <option value="">Selecione...</option>
                                                @if(!empty($item['product_id']))
                                                    @foreach($this->getOptionsForProduct($item['product_id']) as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error("items.{$index}.option_id") <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="mb-1.5 block text-xs font-bold text-foreground">Qtd. <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <input type="number" wire:model.blur="items.{{ $index }}.quantity" step="1" min="{{ $item['min_quantity'] ?? 1 }}" class="w-full rounded-xl border border-border/50 bg-white px-4 py-3 text-sm font-bold shadow-sm transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" {{ empty($item['option_id']) ? 'disabled' : '' }} />
                                                @if(!empty($item['unit']))
                                                    <span class="absolute inset-y-0 right-3 flex items-center text-xs font-medium text-muted-foreground pointer-events-none">{{ $item['unit'] }}</span>
                                                @endif
                                            </div>
                                            @error("items.{$index}.quantity") <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <button type="button" wire:click="addItem" class="flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-border/80 bg-transparent px-5 py-4 text-sm font-bold text-muted-foreground transition-all hover:border-primary/50 hover:bg-primary/5 hover:text-primary">
                                <i data-lucide="plus" class="h-4 w-4"></i>
                                Adicionar outro item
                            </button>
                        </div>
                        
                        <div class="mt-10 mb-2 border-t border-border/50 pt-8">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-500">
                                    <i data-lucide="image" class="h-5 w-5"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-foreground">Fotos da obra (opcional)</h4>
                                    <p class="text-xs text-muted-foreground mb-4">Ajude nossa equipe a entender melhor sua necessidade para entrega.</p>
                                    
                                    <div class="relative overflow-hidden rounded-2xl border-2 border-dashed border-border/60 bg-muted/10 p-6 text-center transition-colors hover:border-primary/50 hover:bg-muted/20">
                                        <input type="file" wire:model="photos" multiple accept="image/*" class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0" />
                                        <div class="pointer-events-none flex flex-col items-center gap-2">
                                            <i data-lucide="cloud-upload" class="h-8 w-8 text-primary"></i>
                                            <strong class="text-sm font-bold text-foreground">Arraste e solte suas fotos aqui</strong>
                                            <span class="text-xs text-muted-foreground">ou clique para selecionar</span>
                                        </div>
                                    </div>
                                    @if(count($photos))
                                        <div class="mt-4 flex flex-wrap gap-3">
                                            @foreach($photos as $idx => $photo)
                                                <div class="relative h-16 w-16 overflow-hidden rounded-lg border border-border shadow-sm">
                                                    <img src="{{ $photo->temporaryUrl() }}" class="h-full w-full object-cover" />
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    @elseif ($currentStep === 2)
                        
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold tracking-tight text-foreground">Obra e Contato</h3>
                            <p class="text-sm text-muted-foreground mt-1">Preencha os dados do local da obra e seu contato para envio do orçamento.</p>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2 mb-8">
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-xs font-bold text-foreground">Nome Completo <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="customer_name" placeholder="Seu nome" class="w-full rounded-xl border border-border/50 bg-white px-4 py-3 text-sm font-medium shadow-sm transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                                @error('customer_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-foreground">WhatsApp <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="customer_phone" placeholder="(00) 00000-0000" class="w-full rounded-xl border border-border/50 bg-white px-4 py-3 text-sm font-medium shadow-sm transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" x-mask="(99) 99999-9999" />
                                @error('customer_phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-foreground">E-mail <span class="text-red-500">*</span></label>
                                <input type="email" wire:model="customer_email" placeholder="seu@email.com" class="w-full rounded-xl border border-border/50 bg-white px-4 py-3 text-sm font-medium shadow-sm transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                                @error('customer_email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <hr class="border-border/50 mb-8" />

                        <div class="grid gap-6 md:grid-cols-12">
                            <div class="md:col-span-4">
                                <label class="mb-1.5 block text-xs font-bold text-foreground">CEP da Obra <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" wire:model.blur="postcode" placeholder="00000-000" class="w-full rounded-xl border border-border/50 bg-white px-4 py-3 text-sm font-medium shadow-sm transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" x-mask="99999-999" />
                                    <div wire:loading wire:target="postcode" class="absolute right-3 top-3">
                                        <i data-lucide="loader-2" class="h-4 w-4 animate-spin text-muted-foreground"></i>
                                    </div>
                                </div>
                                @error('postcode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="md:col-span-6">
                                <label class="mb-1.5 block text-xs font-bold text-foreground">Rua/Av</label>
                                <input type="text" wire:model="street" disabled class="w-full rounded-xl border border-transparent bg-muted/40 px-4 py-3 text-sm font-medium text-muted-foreground shadow-inner" />
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-xs font-bold text-foreground">Nº <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="number" placeholder="Nº ou KM" class="w-full rounded-xl border border-border/50 bg-white px-4 py-3 text-sm font-medium shadow-sm transition-colors focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                                @error('number') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($street)
                                <div class="md:col-span-5">
                                    <label class="mb-1.5 block text-xs font-bold text-foreground">Bairro</label>
                                    <input type="text" wire:model="neighborhood" disabled class="w-full rounded-xl border border-transparent bg-muted/40 px-4 py-3 text-sm font-medium text-muted-foreground shadow-inner" />
                                </div>
                                <div class="md:col-span-5">
                                    <label class="mb-1.5 block text-xs font-bold text-foreground">Cidade</label>
                                    <input type="text" wire:model="city" disabled class="w-full rounded-xl border border-transparent bg-muted/40 px-4 py-3 text-sm font-medium text-muted-foreground shadow-inner" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1.5 block text-xs font-bold text-foreground">UF</label>
                                    <input type="text" wire:model="state" disabled class="w-full rounded-xl border border-transparent bg-muted/40 px-4 py-3 text-sm font-medium text-muted-foreground shadow-inner text-center" />
                                </div>
                            @endif
                        </div>

                    @elseif ($currentStep === 3)
                        <div class="mb-8 text-center">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <i data-lucide="eye" class="h-8 w-8"></i>
                            </div>
                            <h3 class="text-2xl font-bold tracking-tight text-foreground">Quase lá, {{ explode(' ', trim($customer_name))[0] ?? 'tudo certo' }}!</h3>
                            <p class="text-sm text-muted-foreground mt-1">Confira o resumo do seu pedido no quadro ao lado e clique em enviar para concluir.</p>
                        </div>
                        
                        <div class="rounded-2xl border border-border/60 bg-muted/10 p-6">
                            <h4 class="font-bold text-foreground mb-4 border-b border-border/50 pb-2">Seus Dados</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div><span class="text-muted-foreground block text-xs">Nome:</span> <strong class="text-foreground">{{ $customer_name }}</strong></div>
                                <div><span class="text-muted-foreground block text-xs">WhatsApp:</span> <strong class="text-foreground">{{ $customer_phone }}</strong></div>
                                <div><span class="text-muted-foreground block text-xs">E-mail:</span> <strong class="text-foreground">{{ $customer_email }}</strong></div>
                            </div>
                            
                            <h4 class="font-bold text-foreground mt-6 mb-4 border-b border-border/50 pb-2">Local da Obra</h4>
                            <div class="text-sm text-foreground">
                                {{ $street }}, {{ $number }} - {{ $neighborhood }}<br>
                                {{ $city }} - {{ $state }}, CEP: {{ $postcode }}
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Botões de Navegação -->
                <div class="flex items-center justify-between px-2">
                    @if ($currentStep > 1)
                        <button type="button" wire:click="setStep({{ $currentStep - 1 }})" class="rounded-xl border border-border bg-white px-6 py-3.5 text-sm font-bold text-muted-foreground shadow-sm transition-all hover:bg-muted hover:text-foreground">
                            Voltar
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if ($currentStep < 3)
                        <button type="button" wire:click="setStep({{ $currentStep + 1 }})" class="flex items-center gap-2 rounded-xl px-8 py-3.5 text-sm font-bold text-white shadow-md transition-all hover:-translate-y-0.5 hover:shadow-lg {{ $accentBg }}">
                            Continuar 
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </button>
                    @else
                        <button type="button" wire:click="submit" class="flex items-center gap-2 rounded-xl px-10 py-4 text-sm font-bold text-white shadow-md shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-lg {{ $accentBg }}">
                            Enviar Solicitação Grátis
                            <i data-lucide="check-circle" class="h-4 w-4"></i>
                        </button>
                    @endif
                </div>

                @if($turnstileEnabled)
                    <div class="mt-2 text-center">
                        @error('turnstileToken') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            <!-- Direita: Sidebar Resumo -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                
                <div class="rounded-3xl border {{ $boxBorder }} {{ $boxBg }} p-6 shadow-sm sticky top-8">
                    <div class="mb-5 flex items-center gap-3 border-b border-border/50 pb-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $accentLightBg }} {{ $accentText }}">
                            <i data-lucide="file-text" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-foreground">Resumo do pedido</h3>
                            <p class="text-xs text-muted-foreground">Acompanhe os itens adicionados</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        @php $validItemsCount = 0; @endphp
                        @foreach($items as $item)
                            @if(!empty($item['product_id']) && !empty($item['option_id']))
                                @php $validItemsCount++; @endphp
                                <div class="rounded-xl border border-border/60 bg-white p-4 shadow-sm relative">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-muted text-muted-foreground">
                                            <i data-lucide="package" class="h-3 w-3"></i>
                                        </div>
                                        <div>
                                            <strong class="block text-sm font-bold text-foreground leading-tight">{{ $allProducts[$item['product_id']] ?? 'Produto' }}</strong>
                                            <span class="block text-xs text-muted-foreground mt-0.5">{{ $this->getOptionsForProduct($item['product_id'])[$item['option_id']] ?? '' }}</span>
                                            <div class="mt-2 flex items-baseline gap-1">
                                                <span class="text-lg font-extrabold text-primary">{{ $item['quantity'] }}</span>
                                                <span class="text-xs font-medium text-primary/80">{{ $item['unit'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if($validItemsCount === 0)
                            <div class="rounded-xl border border-dashed border-border/80 bg-muted/20 p-6 text-center text-sm text-muted-foreground">
                                Nenhum item preenchido.
                            </div>
                        @else
                            <div class="rounded-xl bg-muted/50 p-3 text-center text-xs font-bold text-foreground">
                                {{ $validItemsCount }} item(s) no pedido
                            </div>
                        @endif
                    </div>

                    @if($validItemsCount > 0)
                        <div class="mt-5 rounded-xl border border-orange-200 bg-orange-50 p-4 text-xs leading-relaxed text-orange-800">
                            <div class="flex items-start gap-2">
                                <i data-lucide="info" class="h-4 w-4 shrink-0 mt-0.5 text-orange-500"></i>
                                <p>Os valores exatos e o custo de entrega serão calculados pela nossa equipe técnica e enviados no seu e-mail ou whatsapp em até 48 horas.</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 flex flex-col gap-4 border-t border-border/50 pt-6">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                                <i data-lucide="shield-check" class="h-4 w-4"></i>
                            </div>
                            <div>
                                <strong class="block text-sm font-bold text-foreground">Atendimento especializado</strong>
                                <span class="block text-xs text-muted-foreground">Orientação técnica para o traço ideal.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                                <i data-lucide="truck" class="h-4 w-4"></i>
                            </div>
                            <div>
                                <strong class="block text-sm font-bold text-foreground">Frota própria</strong>
                                <span class="block text-xs text-muted-foreground">Mais controle e pontualidade na entrega.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                                <i data-lucide="clock" class="h-4 w-4"></i>
                            </div>
                            <div>
                                <strong class="block text-sm font-bold text-foreground">Entrega programada</strong>
                                <span class="block text-xs text-muted-foreground">Concreto no horário da sua obra.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

    @push('scripts')
        @if($turnstileEnabled)
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            <script>
                function onTurnstileSuccess(token) {
                    @this.set('turnstileToken', token);
                }
            </script>
        @endif
    @endpush

    <script>
        document.addEventListener('livewire:initialized', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        
        document.addEventListener('livewire:navigated', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Alpine Mask Setup se necessário no layout pai, garantido aqui:
        document.addEventListener('alpine:init', () => {
            // Opcional: injetar máscara customizada se x-mask não carregar sozinho
        })
    </script>
</div>