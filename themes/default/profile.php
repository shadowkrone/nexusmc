<?php $pageTitle = e($user['username']); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
  <div class="glass rounded-2xl overflow-hidden">
    <!-- Banner / Header -->
    <div class="h-32 gradient-brand relative"></div>
    <div class="px-6 pb-6">
      <div class="flex items-end gap-5 -mt-12 mb-6">
        <img src="<?= mcHead(e($user['username']), 80) ?>" class="w-20 h-20 rounded-2xl border-4 border-slate-900 shadow-xl" alt="">
        <div class="pb-2 flex-1 min-w-0">
          <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-2xl font-bold text-white"><?= e($user['username']) ?></h1>
            <span class="text-xs px-2 py-0.5 rounded-full <?= \App\Models\User::roleBadgeClass($user['role']) ?>">
              <?= \App\Models\User::roleLabel($user['role']) ?>
            </span>
          </div>
          <p class="text-sm text-slate-400 mt-0.5"><?= t('profile.member_since') ?> <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
        </div>
        <?php if(auth()->check() && auth()->user()['username'] === $user['username']): ?>
        <a href="<?= url('settings') ?>" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 text-sm font-medium rounded-lg transition-colors">
          <?= t('profile.edit_profile') ?>
        </a>
        <?php endif; ?>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-slate-900/50 rounded-xl p-4 text-center">
          <p class="text-2xl font-bold text-white"><?= number_format($threadCount) ?></p>
          <p class="text-xs text-slate-500 mt-0.5"><?= t('profile.threads') ?></p>
        </div>
        <div class="bg-slate-900/50 rounded-xl p-4 text-center">
          <p class="text-2xl font-bold text-white"><?= number_format($postCount) ?></p>
          <p class="text-xs text-slate-500 mt-0.5"><?= t('profile.replies') ?></p>
        </div>
        <div class="bg-slate-900/50 rounded-xl p-4 text-center">
          <p class="text-sm font-semibold text-white"><?= timeAgo($user['last_seen']) ?></p>
          <p class="text-xs text-slate-500 mt-0.5"><?= t('profile.last_seen') ?></p>
        </div>
      </div>
    </div>
  </div>
</div>
