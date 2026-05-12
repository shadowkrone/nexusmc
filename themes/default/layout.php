<!DOCTYPE html>
<html lang="<?= setting('language', 'en') ?>" class="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e(setting('site_name', APP_NAME)) ?><?= isset($pageTitle) ? ' — ' . e($pageTitle) : '' ?></title>
<meta name="description" content="<?= e(setting('site_description', 'Minecraft Community')) ?>">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        brand: { DEFAULT:'#10b981', dark:'#059669', light:'#34d399', glow:'rgba(16,185,129,0.15)' }
      },
      fontFamily: { sans: ['Inter','system-ui','sans-serif'] }
    }
  }
}
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root { color-scheme: dark; }
  body { background-color: #0b0f19; }
  .gradient-brand { background: linear-gradient(135deg,#10b981 0%,#0891b2 100%); }
  .glass { background:rgba(15,23,42,0.8); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.06); }
  .glow-brand { box-shadow: 0 0 30px rgba(16,185,129,0.15); }
  .prose-dark p { color:#cbd5e1; margin-bottom:.75rem; line-height:1.75; }
  .prose-dark pre { background:#0f172a; border:1px solid #1e293b; border-radius:.5rem; padding:1rem; overflow-x:auto; }
  .prose-dark code { background:#1e293b; border-radius:.25rem; padding:.1rem .3rem; font-size:.875em; color:#e2e8f0; }
  .prose-dark blockquote { border-left:3px solid #10b981; padding-left:1rem; color:#94a3b8; }
  ::-webkit-scrollbar { width:6px; } ::-webkit-scrollbar-track { background:#0f172a; }
  ::-webkit-scrollbar-thumb { background:#334155; border-radius:3px; }
  [x-cloak] { display:none!important; }
</style>
</head>
<body class="text-slate-100 font-sans antialiased min-h-screen flex flex-col">

<?php $siteName = setting('site_name', APP_NAME); ?>

<!-- NAVIGATION -->
<nav class="sticky top-0 z-50 glass border-b border-slate-800/50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between h-16">

      <!-- Logo -->
      <a href="<?= url() ?>" class="flex items-center gap-2.5 group">
        <?php if($logo = setting('site_logo')): ?>
          <img src="<?= e($logo) ?>" alt="Logo" class="h-8 w-8 rounded">
        <?php else: ?>
          <div class="w-8 h-8 gradient-brand rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2L2 7l8 5 8-5-8-5zM2 12l8 5 8-5M2 17l8 5 8-5"/></svg>
          </div>
        <?php endif; ?>
        <span class="font-bold text-lg text-white"><?= e($siteName) ?></span>
      </a>

      <!-- Desktop Nav -->
      <div class="hidden md:flex items-center gap-1">
        <a href="<?= url() ?>" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors"><?= t('nav.home') ?></a>
        <a href="<?= url('forum') ?>" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors"><?= t('nav.forum') ?></a>
        <?php if(setting('store_url')): ?>
        <a href="<?= e(setting('store_url')) ?>" target="_blank" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors"><?= t('nav.shop') ?></a>
        <?php endif; ?>
        <?php if(setting('discord_url')): ?>
        <a href="<?= e(setting('discord_url')) ?>" target="_blank" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors flex items-center gap-1.5">
          <svg class="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515..."/></svg>
          Discord
        </a>
        <?php endif; ?>
      </div>

      <!-- User Menu -->
      <div class="flex items-center gap-3">
        <?php if(auth()->check()): $u = auth()->user(); ?>
          <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 transition-colors">
              <img src="<?= mcHead(e($u['username']), 24) ?>" class="w-6 h-6 rounded" alt="">
              <span class="text-sm font-medium text-slate-200"><?= e($u['username']) ?></span>
              <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-cloak @click.outside="open = false" x-transition
                 class="absolute right-0 mt-2 w-48 glass rounded-xl py-1 shadow-xl border border-slate-700">
              <a href="<?= url('user/' . urlencode($u['username'])) ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                <?= t('nav.my_profile') ?>
              </a>
              <a href="<?= url('settings') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-slate-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                <?= t('nav.settings') ?>
              </a>
              <?php if(auth()->isAdmin()): ?>
              <div class="border-t border-slate-700 my-1"></div>
              <a href="<?= url('admin') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-brand hover:bg-slate-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <?= t('nav.admin_panel') ?>
              </a>
              <?php endif; ?>
              <div class="border-t border-slate-700 my-1"></div>
              <a href="<?= url('logout') ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-slate-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <?= t('nav.logout') ?>
              </a>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= url('login') ?>" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white transition-colors"><?= t('nav.login') ?></a>
          <a href="<?= url('register') ?>" class="px-4 py-2 text-sm font-semibold bg-brand hover:bg-brand-dark text-white rounded-lg transition-colors shadow-lg"><?= t('nav.register') ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- FLASH MESSAGES -->
<?php if($msg = flash('success')): ?>
<div x-data="{show:true}" x-show="show" x-transition class="bg-emerald-500/10 border-b border-emerald-500/20 text-emerald-400 text-sm px-4 py-2.5 text-center flex items-center justify-center gap-2">
  <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
  <?= e($msg) ?>
  <button @click="show=false" class="ml-4 opacity-60 hover:opacity-100">✕</button>
</div>
<?php endif; ?>

<!-- CONTENT -->
<main class="flex-1">
  <?= $content ?>
</main>

<!-- FOOTER -->
<footer class="border-t border-slate-800 mt-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div>
        <div class="flex items-center gap-2 mb-3">
          <div class="w-7 h-7 gradient-brand rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2L2 7l8 5 8-5-8-5z"/></svg>
          </div>
          <span class="font-bold text-white"><?= e($siteName) ?></span>
        </div>
        <p class="text-sm text-slate-500"><?= e(setting('site_description', 'Minecraft Community')) ?></p>
      </div>
      <div>
        <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3"><?= t('footer.links') ?></h4>
        <ul class="space-y-2 text-sm text-slate-500">
          <li><a href="<?= url() ?>" class="hover:text-brand transition-colors"><?= t('nav.home') ?></a></li>
          <li><a href="<?= url('forum') ?>" class="hover:text-brand transition-colors"><?= t('nav.forum') ?></a></li>
          <?php if(setting('discord_url')): ?>
          <li><a href="<?= e(setting('discord_url')) ?>" class="hover:text-brand transition-colors">Discord</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <div>
        <h4 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3"><?= t('footer.powered_by') ?></h4>
        <p class="text-sm text-slate-600">NexusMC v<?= APP_VERSION ?> — <a href="https://github.com" class="hover:text-brand transition-colors">Open Source</a></p>
      </div>
    </div>
  </div>
</footer>

<script src="<?= url('public/js/app.js') ?>"></script>
</body>
</html>
