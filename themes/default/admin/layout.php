<!DOCTYPE html>
<html lang="<?= setting('language', 'en') ?>" class="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — <?= e(setting('site_name', APP_NAME)) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  darkMode:'class',
  theme:{ extend:{ colors:{ brand:{ DEFAULT:'#10b981', dark:'#059669', light:'#34d399' } }, fontFamily:{ sans:['Inter','system-ui','sans-serif'] } } }
}
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{color-scheme:dark;} body{background:#080d14;}
  .glass{background:rgba(15,23,42,0.8);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.06);}
  .gradient-brand{background:linear-gradient(135deg,#10b981 0%,#0891b2 100%);}
  [x-cloak]{display:none!important;}
</style>
</head>
<body class="text-slate-100 font-sans antialiased">
<div class="flex min-h-screen" x-data="{sidebarOpen:false}">

  <!-- Sidebar -->
  <aside class="w-60 flex-shrink-0 flex flex-col bg-slate-950 border-r border-slate-800/50 hidden lg:flex">
    <div class="h-16 flex items-center px-5 border-b border-slate-800/50">
      <div class="flex items-center gap-2.5">
        <div class="w-7 h-7 gradient-brand rounded-lg flex items-center justify-center">
          <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2L2 7l8 5 8-5-8-5z"/></svg>
        </div>
        <div>
          <p class="font-bold text-white text-sm">NexusMC</p>
          <p class="text-xs text-brand">Admin Panel</p>
        </div>
      </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-0.5">
      <?php
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $navLinks = [
          ['icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6','label'=>t('admin.nav.dashboard'),'href'=>'admin'],
          ['icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z','label'=>t('admin.nav.users'),'href'=>'admin/users'],
          ['icon'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z','label'=>t('admin.nav.forum'),'href'=>'admin/forum'],
          ['icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z','label'=>t('admin.nav.settings'),'href'=>'admin/settings'],
          ['icon'=>'M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z','label'=>t('admin.nav.plugins'),'href'=>'admin/plugins'],
          ['icon'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4','label'=>t('admin.nav.updates'),'href'=>'admin/updates'],
        ];
        foreach($navLinks as $l):
          $active = str_ends_with($currentPath, '/' . $l['href']) || str_ends_with($currentPath, '/' . $l['href'] . '/');
      ?>
      <a href="<?= url($l['href']) ?>"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors <?= $active ? 'bg-brand/15 text-brand' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
        <svg class="w-4.5 h-4.5 w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
          <path stroke-linecap="round" stroke-linejoin="round" d="<?= $l['icon'] ?>"/>
        </svg>
        <?= $l['label'] ?>
      </a>
      <?php endforeach; ?>
      <?= app()->plugins->fire('admin_nav_html', '') ?>
    </nav>

    <div class="px-3 pb-4 border-t border-slate-800/50 pt-3">
      <a href="<?= url() ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-500 hover:text-white transition-colors rounded-lg hover:bg-slate-800">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <?= t('admin.back_to_site') ?>
      </a>
    </div>
  </aside>

  <!-- Main -->
  <div class="flex-1 flex flex-col min-w-0">
    <!-- Top bar -->
    <header class="h-16 flex items-center justify-between px-6 bg-slate-950/80 border-b border-slate-800/50 backdrop-blur-sm sticky top-0 z-30">
      <div class="flex items-center gap-4">
        <div class="hidden lg:block">
          <p class="text-sm text-slate-400">Admin Panel</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-slate-400"><?= e(auth()->user()['username']) ?></span>
        <img src="<?= mcHead(e(auth()->user()['username']), 32) ?>" class="w-8 h-8 rounded-lg" alt="">
      </div>
    </header>

    <main class="flex-1 p-6 overflow-auto">
      <?php if($success = flash('success')): ?>
      <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 mb-5 flex items-center gap-2 text-emerald-400 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <?= e($success) ?>
      </div>
      <?php endif; ?>
      <?= $content ?>
    </main>
  </div>
</div>
</body>
</html>
