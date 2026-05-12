<?php $pageTitle = t('forum.title'); ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">

  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-3xl font-bold text-white"><?= t('forum.title') ?></h1>
      <p class="text-slate-400 mt-1"><?= t('forum.subtitle') ?></p>
    </div>
  </div>

  <?php if(empty($categories)): ?>
  <div class="glass rounded-2xl p-12 text-center">
    <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
      <svg class="w-7 h-7 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    </div>
    <p class="text-slate-400 font-medium mb-1"><?= t('forum.no_categories') ?></p>
    <?php if(auth()->isAdmin()): ?>
    <p class="text-sm text-slate-600"><?= t('forum.create_in_admin') ?> <a href="<?= url('admin/forum') ?>" class="text-brand hover:underline"><?= t('forum.admin_forum_label') ?></a>.</p>
    <?php endif; ?>
  </div>
  <?php else: ?>

  <div class="space-y-3">
    <?php foreach($categories as $cat): ?>
    <a href="<?= url('forum/category/' . urlencode($cat['slug'])) ?>"
       class="glass rounded-2xl p-5 flex items-center gap-5 hover:border-slate-600 hover:bg-slate-800/30 transition-all group block">

      <!-- Icon -->
      <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0"
           style="background:<?= e($cat['color'] ?? '#10b981') ?>20;border:1px solid <?= e($cat['color'] ?? '#10b981') ?>30">
        <?= e($cat['icon'] ?? '💬') ?>
      </div>

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <h3 class="font-bold text-white group-hover:text-brand transition-colors"><?= e($cat['name']) ?></h3>
        <?php if($cat['description']): ?>
        <p class="text-sm text-slate-400 mt-0.5 truncate"><?= e($cat['description']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Stats -->
      <div class="hidden sm:flex items-center gap-6 text-center flex-shrink-0">
        <div>
          <p class="text-lg font-bold text-white"><?= number_format((int)$cat['thread_count']) ?></p>
          <p class="text-xs text-slate-500"><?= t('forum.threads') ?></p>
        </div>
        <div>
          <p class="text-lg font-bold text-white"><?= number_format((int)$cat['post_count']) ?></p>
          <p class="text-xs text-slate-500"><?= t('forum.replies') ?></p>
        </div>
      </div>

      <!-- Last activity -->
      <?php if($cat['last_thread_title']): ?>
      <div class="hidden lg:block text-right flex-shrink-0 max-w-[180px]">
        <p class="text-xs text-slate-500 truncate"><?= e(mb_strimwidth($cat['last_thread_title'], 0, 30, '…')) ?></p>
        <p class="text-xs text-slate-600 mt-0.5"><?= timeAgo($cat['last_activity']) ?></p>
      </div>
      <?php endif; ?>

      <svg class="w-5 h-5 text-slate-600 group-hover:text-brand transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
