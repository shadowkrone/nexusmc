<?php $pageTitle = t('nav.home'); ?>

<!-- HERO -->
<section class="relative overflow-hidden">
  <div class="absolute inset-0 bg-gradient-to-br from-emerald-950/40 via-transparent to-cyan-950/30 pointer-events-none"></div>
  <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-brand/5 rounded-full blur-3xl pointer-events-none"></div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 pt-20 pb-16">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
      <div>
        <?php if(auth()->check()): ?>
          <div class="inline-flex items-center gap-2 bg-brand/10 border border-brand/20 text-brand text-sm font-medium px-3 py-1 rounded-full mb-6">
            <span class="w-2 h-2 bg-brand rounded-full animate-pulse"></span>
            <?= t('home.welcome_back', ['name' => e(auth()->user()['username'])]) ?>
          </div>
        <?php else: ?>
          <div class="inline-flex items-center gap-2 bg-brand/10 border border-brand/20 text-brand text-sm font-medium px-3 py-1 rounded-full mb-6">
            <span class="w-2 h-2 bg-brand rounded-full animate-pulse"></span>
            <?= t('home.members_count', ['count' => $totalUsers]) ?>
          </div>
        <?php endif; ?>

        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-5 text-white">
          <?= t('home.welcome_to') ?><br>
          <span class="text-transparent bg-clip-text gradient-brand"><?= e(setting('site_name', APP_NAME)) ?></span>
        </h1>
        <p class="text-lg text-slate-400 mb-8 leading-relaxed">
          <?= e(setting('site_description', 'Join our Minecraft community — discuss, play and have fun with other players.')) ?>
        </p>
        <div class="flex flex-wrap gap-3">
          <?php if(!auth()->check()): ?>
          <a href="<?= url('register') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-brand hover:bg-brand-dark text-white font-semibold rounded-xl transition-all hover:scale-105 shadow-lg shadow-brand/25">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <?= t('home.join_free') ?>
          </a>
          <?php endif; ?>
          <a href="<?= url('forum') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-xl transition-all border border-slate-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <?= t('home.go_to_forum') ?>
          </a>
        </div>
      </div>

      <!-- Server Status Card -->
      <div>
        <?php if($serverIp && $serverStatus): ?>
        <div class="glass rounded-2xl p-6 glow-brand">
          <div class="flex items-center justify-between mb-5">
            <div>
              <h3 class="font-semibold text-white text-lg"><?= t('home.server_status') ?></h3>
              <p class="text-sm text-slate-400"><?= e($serverIp) ?></p>
            </div>
            <?php if($serverStatus['online']): ?>
            <div class="flex items-center gap-2 bg-emerald-500/15 border border-emerald-500/25 text-emerald-400 text-sm font-semibold px-3 py-1.5 rounded-full">
              <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
              <?= t('home.online') ?>
            </div>
            <?php else: ?>
            <div class="flex items-center gap-2 bg-red-500/15 border border-red-500/25 text-red-400 text-sm font-semibold px-3 py-1.5 rounded-full">
              <span class="w-2 h-2 bg-red-400 rounded-full"></span>
              <?= t('home.offline') ?>
            </div>
            <?php endif; ?>
          </div>

          <?php if($serverStatus['online']): ?>
          <div class="space-y-4">
            <?php if($serverStatus['motd']): ?>
            <p class="text-slate-300 text-sm bg-slate-900/50 rounded-lg px-3 py-2 font-mono"><?= e($serverStatus['motd']) ?></p>
            <?php endif; ?>

            <div class="grid grid-cols-2 gap-3">
              <div class="bg-slate-900/50 rounded-xl p-4">
                <p class="text-slate-500 text-xs uppercase tracking-wider mb-1"><?= t('home.players') ?></p>
                <p class="text-2xl font-bold text-white"><?= $serverStatus['players_online'] ?><span class="text-base text-slate-500 font-normal"> / <?= $serverStatus['players_max'] ?></span></p>
              </div>
              <div class="bg-slate-900/50 rounded-xl p-4">
                <p class="text-slate-500 text-xs uppercase tracking-wider mb-1"><?= t('home.version') ?></p>
                <p class="text-sm font-semibold text-white mt-1"><?= e($serverStatus['version']) ?></p>
              </div>
            </div>

            <!-- Player bar -->
            <div>
              <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                <span><?= t('home.capacity') ?></span>
                <span><?= round($serverStatus['players_online'] / max(1,$serverStatus['players_max']) * 100) ?>%</span>
              </div>
              <div class="h-2 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full gradient-brand rounded-full transition-all"
                     style="width:<?= min(100, round($serverStatus['players_online'] / max(1,$serverStatus['players_max']) * 100)) ?>%"></div>
              </div>
            </div>

            <!-- IP copy -->
            <div x-data="{copied:false}" class="flex items-center gap-2 bg-slate-900/50 rounded-xl px-4 py-3">
              <code class="flex-1 text-brand text-sm font-mono"><?= e($serverIp) ?></code>
              <button @click="navigator.clipboard.writeText('<?= e($serverIp) ?>');copied=true;setTimeout(()=>copied=false,2000)"
                      class="text-slate-400 hover:text-white transition-colors">
                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                <svg x-show="copied" x-cloak class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              </button>
            </div>
          </div>
          <?php else: ?>
          <p class="text-slate-500 text-center py-8"><?= t('home.server_offline_msg') ?></p>
          <?php endif; ?>
        </div>
        <?php else: ?>
        <!-- Stats cards when no server -->
        <div class="grid grid-cols-3 gap-4">
          <div class="glass rounded-2xl p-5 text-center">
            <p class="text-3xl font-extrabold text-white mb-1"><?= number_format($totalUsers) ?></p>
            <p class="text-xs text-slate-500 uppercase tracking-wider"><?= t('home.members') ?></p>
          </div>
          <div class="glass rounded-2xl p-5 text-center">
            <p class="text-3xl font-extrabold text-white mb-1"><?= number_format($totalThreads) ?></p>
            <p class="text-xs text-slate-500 uppercase tracking-wider"><?= t('home.threads') ?></p>
          </div>
          <div class="glass rounded-2xl p-5 text-center">
            <p class="text-3xl font-extrabold text-white mb-1"><?= number_format($totalPosts) ?></p>
            <p class="text-xs text-slate-500 uppercase tracking-wider"><?= t('home.replies') ?></p>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- STATS BAR -->
<?php if($serverIp && $serverStatus && $serverStatus['online']): ?>
<section class="border-y border-slate-800/50 bg-slate-900/30">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex flex-wrap gap-6 justify-center">
    <?php foreach([[t('home.members'),$totalUsers],[t('home.threads'),$totalThreads],[t('home.replies'),$totalPosts]] as [$label,$val]): ?>
    <div class="flex items-center gap-3">
      <span class="text-2xl font-bold text-white"><?= number_format($val) ?></span>
      <span class="text-sm text-slate-500"><?= $label ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- MAIN CONTENT -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
  <div class="grid lg:grid-cols-3 gap-8">

    <!-- Recent Threads -->
    <div class="lg:col-span-2">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-xl font-bold text-white"><?= t('home.recent_threads') ?></h2>
        <a href="<?= url('forum') ?>" class="text-sm text-brand hover:text-brand-light transition-colors"><?= t('home.see_all') ?></a>
      </div>
      <div class="space-y-2">
        <?php if(empty($recentThreads)): ?>
        <div class="glass rounded-xl p-8 text-center">
          <p class="text-slate-500"><?= t('home.no_threads') ?></p>
          <?php if(auth()->check()): ?>
          <a href="<?= url('forum') ?>" class="inline-block mt-3 text-sm text-brand hover:underline"><?= t('home.go_to_forum') ?></a>
          <?php endif; ?>
        </div>
        <?php else: ?>
        <?php foreach($recentThreads as $t_): ?>
        <a href="<?= url('forum/thread/' . $t_['id']) ?>" class="glass rounded-xl px-4 py-3.5 flex items-center gap-4 hover:border-brand/30 hover:bg-slate-800/40 transition-all group block">
          <img src="<?= mcHead(e($t_['username']), 40) ?>" class="w-10 h-10 rounded-lg flex-shrink-0" alt="">
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-white group-hover:text-brand transition-colors truncate"><?= e($t_['title']) ?></p>
            <p class="text-xs text-slate-500 mt-0.5">
              <span class="text-slate-400"><?= e($t_['username']) ?></span>
              · <?= e($t_['category_name']) ?>
              · <?= timeAgo($t_['updated_at']) ?>
            </p>
          </div>
          <svg class="w-4 h-4 text-slate-600 group-hover:text-brand transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
      <!-- Join CTA -->
      <?php if(!auth()->check()): ?>
      <div class="glass rounded-2xl p-6 border border-brand/20">
        <h3 class="font-bold text-white mb-2"><?= t('home.join_community') ?></h3>
        <p class="text-sm text-slate-400 mb-4"><?= t('home.join_community_desc') ?></p>
        <a href="<?= url('register') ?>" class="block text-center py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors"><?= t('home.create_account') ?></a>
        <p class="text-xs text-center text-slate-600 mt-2"><?= t('home.have_account') ?> <a href="<?= url('login') ?>" class="text-brand hover:underline"><?= t('nav.login') ?></a></p>
      </div>
      <?php endif; ?>

      <!-- Recent Activity -->
      <div class="glass rounded-2xl p-5">
        <h3 class="font-semibold text-white mb-4"><?= t('home.recent_activity') ?></h3>
        <?php if(empty($recentPosts)): ?>
          <p class="text-sm text-slate-500"><?= t('home.no_activity') ?></p>
        <?php else: ?>
        <ul class="space-y-3">
          <?php foreach(array_slice($recentPosts, 0, 5) as $p): ?>
          <li class="flex gap-3 items-start">
            <img src="<?= mcHead(e($p['username']), 28) ?>" class="w-7 h-7 rounded flex-shrink-0 mt-0.5" alt="">
            <div class="min-w-0">
              <p class="text-sm text-slate-300">
                <span class="font-medium text-white"><?= e($p['username']) ?></span>
                <?= t('home.replied_in') ?>
                <a href="<?= url('forum/thread/' . $p['thread_id']) ?>" class="text-brand hover:underline truncate"><?= e(mb_strimwidth($p['thread_title'], 0, 40, '…')) ?></a>
              </p>
              <p class="text-xs text-slate-600 mt-0.5"><?= timeAgo($p['created_at']) ?></p>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
