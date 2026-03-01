<?php
    $website = \App\Models\Setting::get('website', []);
    $websiteName = data_get($website, 'name', 'ConcretoPro');
    $websiteTitle = data_get($website, 'title', 'Concreto Usinado & Equipamentos');
    $websiteDescription = data_get($website, 'description', 'Concreto usinado de alta qualidade e locação de equipamentos.');
    $scripts = data_get($website, 'scripts', []);
    $googleFontsUrl = data_get($scripts, 'google_fonts_url', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap');
    $headScripts = data_get($scripts, 'head_scripts');
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<meta name="theme-color" content="#e5b800">
<meta name="description" content="<?php echo e($websiteDescription); ?>">
<title><?php echo e($websiteName); ?> | <?php echo e($websiteTitle); ?></title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="<?php echo e($googleFontsUrl); ?>" rel="stylesheet">

<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($headScripts): ?>
    <?php echo $headScripts; ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/marvincoelho/projects/codhous/resources/views/components/site-head.blade.php ENDPATH**/ ?>