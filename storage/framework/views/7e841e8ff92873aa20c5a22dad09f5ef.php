<section class="border-b border-border bg-muted/50 py-10 lg:py-12 overflow-hidden">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <p class="mb-8 text-center text-xs font-semibold uppercase tracking-widest text-muted-foreground">Empresas que confiam no nosso concreto</p>
    </div>

    <style>
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-50% - 2rem)); } 
        }
        .partners-track {
            animation: marquee 30s linear infinite;
            width: max-content;
        }
    </style>

    <script>
        function pauseMarquee() {
            document.getElementById('partners-track').style.animationPlayState = 'paused';
        }
        function resumeMarquee() {
            document.getElementById('partners-track').style.animationPlayState = 'running';
        }
    </script>

    
    <div class="partners-marquee relative" onmouseenter="pauseMarquee()" onmouseleave="resumeMarquee()">
        <div class="partners-track flex items-center gap-16" id="partners-track">
            
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="building-2" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">MRV Engenharia</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="hard-hat" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Construtora Tenda</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="landmark" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Cyrela Brazil</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="factory" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Gafisa S.A.</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="warehouse" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Even Construtora</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="hammer" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Direcional Eng.</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="construction" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Cury Construtora</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="ruler" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Plano & Plano</span>
            </div>

            
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="building-2" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">MRV Engenharia</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="hard-hat" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Construtora Tenda</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="landmark" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Cyrela Brazil</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="factory" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Gafisa S.A.</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="warehouse" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Even Construtora</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="hammer" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Direcional Eng.</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="construction" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Cury Construtora</span>
            </div>
            <div class="flex shrink-0 items-center gap-2 opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-foreground/10"><i data-lucide="ruler" class="h-5 w-5 text-foreground"></i></div>
                <span class="font-mono text-sm font-bold text-foreground whitespace-nowrap">Plano & Plano</span>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/components/section-partners.blade.php ENDPATH**/ ?>