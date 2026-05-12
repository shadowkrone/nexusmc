<div class="max-w-4xl">
  <h1 class="text-2xl font-bold text-white mb-7"><?= t('admin.forum.title') ?></h1>

  <?php if($error ?? null): ?>
  <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-3 mb-5 flex items-center gap-2 text-red-400 text-sm">
    <?= e($error) ?>
  </div>
  <?php endif; ?>

  <div class="grid lg:grid-cols-2 gap-6">
    <!-- Create category -->
    <div class="glass rounded-2xl p-6">
      <h2 class="font-semibold text-white mb-5"><?= t('admin.forum.create_category') ?></h2>
      <form method="POST" action="<?= url('admin/forum/category') ?>" class="space-y-4">
        <?= csrf_field() ?>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.forum.name') ?></label>
          <input type="text" name="name" required
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="<?= t('admin.forum.name_ph') ?>">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.forum.description') ?></label>
          <input type="text" name="description"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="<?= t('admin.forum.description_ph') ?>">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.forum.icon') ?></label>
            <input type="text" name="icon" value="💬"
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors text-xl">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.forum.color') ?></label>
            <input type="color" name="color" value="#10b981"
                   class="w-full h-10 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 cursor-pointer">
          </div>
        </div>
        <button type="submit" class="w-full py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">
          <?= t('admin.forum.create_btn') ?>
        </button>
      </form>
    </div>

    <!-- List categories -->
    <div class="glass rounded-2xl overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-800">
        <h2 class="font-semibold text-white"><?= t('admin.forum.existing') ?></h2>
      </div>
      <?php if(empty($categories)): ?>
      <div class="p-8 text-center text-slate-500"><?= t('admin.forum.none_yet') ?></div>
      <?php else: ?>
      <div class="divide-y divide-slate-800/50">
        <?php foreach($categories as $cat): ?>
        <div class="flex items-center gap-3 px-5 py-3">
          <div class="w-9 h-9 rounded-lg flex items-center justify-center text-lg flex-shrink-0"
               style="background:<?= e($cat['color'] ?? '#10b981') ?>20">
            <?= e($cat['icon'] ?? '💬') ?>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-medium text-white text-sm"><?= e($cat['name']) ?></p>
            <?php if($cat['description']): ?><p class="text-xs text-slate-500 truncate"><?= e($cat['description']) ?></p><?php endif; ?>
          </div>
          <form method="POST" action="<?= url('admin/forum/category/' . $cat['id'] . '/delete') ?>"
                onsubmit="return confirm('<?= e(t('admin.forum.delete_confirm')) ?>')">
            <?= csrf_field() ?>
            <button type="submit" class="text-xs px-2.5 py-1 bg-red-500/15 text-red-400 hover:bg-red-500/25 rounded-lg transition-colors">
              <?= t('admin.forum.delete') ?>
            </button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
