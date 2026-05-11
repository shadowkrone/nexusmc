<?php $pageTitle = e($category['name']); ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">

  <!-- Breadcrumb -->
  <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
    <a href="<?= url('forum') ?>" class="hover:text-brand transition-colors">Forum</a>
    <span>›</span>
    <span class="text-slate-300"><?= e($category['name']) ?></span>
  </nav>

  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl"
           style="background:<?= e($category['color'] ?? '#10b981') ?>20;border:1px solid <?= e($category['color'] ?? '#10b981') ?>30">
        <?= e($category['icon'] ?? '💬') ?>
      </div>
      <div>
        <h1 class="text-2xl font-bold text-white"><?= e($category['name']) ?></h1>
        <?php if($category['description']): ?>
        <p class="text-sm text-slate-400"><?= e($category['description']) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php if(auth()->check()): ?>
    <a href="<?= url('forum/new/' . $category['id']) ?>"
       class="flex items-center gap-2 px-5 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-xl transition-colors shadow-lg shadow-brand/20">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Ny tråd
    </a>
    <?php endif; ?>
  </div>

  <?php if(empty($threads)): ?>
  <div class="glass rounded-2xl p-12 text-center">
    <p class="text-slate-400 mb-2">Ingen tråde i denne kategori endnu.</p>
    <?php if(auth()->check()): ?>
    <a href="<?= url('forum/new/' . $category['id']) ?>" class="text-sm text-brand hover:underline">Start den første tråd!</a>
    <?php else: ?>
    <a href="<?= url('register') ?>" class="text-sm text-brand hover:underline">Registrer dig for at skrive</a>
    <?php endif; ?>
  </div>
  <?php else: ?>
  <div class="glass rounded-2xl overflow-hidden">
    <!-- Table header -->
    <div class="grid grid-cols-12 gap-4 px-5 py-3 border-b border-slate-800 text-xs font-medium text-slate-500 uppercase tracking-wider">
      <div class="col-span-7">Tråd</div>
      <div class="col-span-2 text-center hidden sm:block">Svar</div>
      <div class="col-span-2 text-center hidden sm:block">Visninger</div>
      <div class="col-span-1"></div>
    </div>

    <?php foreach($threads as $t): ?>
    <a href="<?= url('forum/thread/' . $t['id']) ?>"
       class="grid grid-cols-12 gap-4 px-5 py-4 border-b border-slate-800/50 hover:bg-slate-800/30 transition-colors group items-center last:border-0">

      <div class="col-span-7 flex items-center gap-3">
        <img src="<?= mcHead(e($t['username']), 36) ?>" class="w-9 h-9 rounded-lg flex-shrink-0" alt="">
        <div class="min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <?php if($t['pinned']): ?>
            <span class="text-xs bg-amber-500/20 text-amber-400 border border-amber-500/25 px-1.5 py-0.5 rounded font-medium">📌 Fast</span>
            <?php endif; ?>
            <?php if($t['locked']): ?>
            <span class="text-xs bg-slate-700 text-slate-400 px-1.5 py-0.5 rounded font-medium">🔒 Låst</span>
            <?php endif; ?>
            <span class="font-semibold text-white group-hover:text-brand transition-colors truncate"><?= e($t['title']) ?></span>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            af <span class="text-slate-400"><?= e($t['username']) ?></span>
            · <?= timeAgo($t['created_at']) ?>
            <?php if($t['last_reply_user'] && $t['last_reply_user'] !== $t['username']): ?>
            · Svar: <span class="text-slate-400"><?= e($t['last_reply_user']) ?></span> <?= timeAgo($t['last_reply_at']) ?>
            <?php endif; ?>
          </p>
        </div>
      </div>

      <div class="col-span-2 text-center hidden sm:block">
        <span class="text-white font-semibold"><?= number_format((int)$t['reply_count']) ?></span>
      </div>
      <div class="col-span-2 text-center hidden sm:block">
        <span class="text-slate-400"><?= number_format((int)$t['views']) ?></span>
      </div>
      <div class="col-span-3 sm:col-span-1 flex justify-end">
        <svg class="w-4 h-4 text-slate-600 group-hover:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if($pages > 1): ?>
  <div class="flex justify-center gap-2 mt-6">
    <?php for($i=1;$i<=$pages;$i++): ?>
    <a href="?page=<?= $i ?>" class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors <?= $i===$page ? 'bg-brand text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' ?>">
      <?= $i ?>
    </a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>
