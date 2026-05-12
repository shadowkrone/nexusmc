<div class="max-w-4xl">
  <div class="flex items-center justify-between mb-7">
    <div>
      <h1 class="text-2xl font-bold text-white"><?= t('admin.plugins.title') ?></h1>
      <p class="text-sm text-slate-400 mt-1"><?= t('admin.plugins.dir_hint') ?> <code class="bg-slate-800 px-1.5 py-0.5 rounded text-xs"><?= t('admin.plugins.folder') ?></code> <?= t('admin.plugins.folder_suffix') ?></p>
    </div>
  </div>

  <?php if(empty($plugins)): ?>
  <div class="glass rounded-2xl p-12 text-center">
    <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
      <svg class="w-7 h-7 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
    </div>
    <p class="text-slate-400 font-medium mb-2"><?= t('admin.plugins.none') ?></p>
    <p class="text-sm text-slate-600"><?= t('admin.plugins.none_hint') ?> <code class="bg-slate-800 px-1.5 py-0.5 rounded"><?= t('admin.plugins.none_folder') ?></code> <?= t('admin.plugins.none_folder_suffix') ?></p>
  </div>
  <?php else: ?>
  <div class="space-y-3">
    <?php foreach($plugins as $name => $info): ?>
    <div class="glass rounded-xl p-5 flex items-center gap-4">
      <div class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-xl flex-shrink-0">
        <?= e($info['icon'] ?? '🔌') ?>
      </div>
      <div class="flex-1 min-w-0">
        <p class="font-semibold text-white"><?= e($info['name'] ?? $name) ?></p>
        <p class="text-xs text-slate-400 mt-0.5"><?= e($info['description'] ?? '') ?><?php if($v = $info['version'] ?? null): ?> · v<?= e($v) ?><?php endif; ?></p>
      </div>
      <form method="POST" action="<?= url('admin/plugins/' . e($name) . '/toggle') ?>">
        <?= csrf_field() ?>
        <button type="submit"
                class="relative inline-flex items-center cursor-pointer focus:outline-none group">
          <span class="w-11 h-6 rounded-full transition-colors <?= $info['enabled'] ? 'bg-brand' : 'bg-slate-700' ?>"></span>
          <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform <?= $info['enabled'] ? 'translate-x-5' : 'translate-x-0' ?>"></span>
        </button>
      </form>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
