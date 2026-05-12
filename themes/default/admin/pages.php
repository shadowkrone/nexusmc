<div class="max-w-4xl" x-data="{ showForm: <?= $editing ? 'false' : 'false' ?> }">

  <div class="flex items-center justify-between mb-7">
    <div>
      <h1 class="text-2xl font-bold text-white"><?= t('admin.pages.title') ?></h1>
      <p class="text-sm text-slate-400 mt-1"><?= t('admin.pages.subtitle') ?></p>
    </div>
    <?php if(!$editing): ?>
    <button @click="showForm = !showForm"
            class="flex items-center gap-2 px-5 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold text-sm rounded-xl transition-colors shadow-lg shadow-brand/20">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      <?= t('admin.pages.new_page') ?>
    </button>
    <?php endif; ?>
  </div>

  <?php if($editing): ?>
  <!-- Edit form -->
  <div class="glass rounded-2xl p-6 mb-6">
    <h2 class="font-semibold text-white mb-5"><?= t('admin.pages.edit_title') ?> — <span class="text-brand"><?= e($editing['title']) ?></span></h2>
    <form method="POST" action="<?= url('admin/pages/' . $editing['id'] . '/edit') ?>">
      <?= csrf_field() ?>
      <div class="space-y-4">
        <div class="grid sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.pages.title_label') ?></label>
            <input type="text" name="title" value="<?= e($editing['title']) ?>" required
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                   placeholder="<?= e(t('admin.pages.title_ph')) ?>">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.pages.sort_order') ?></label>
            <input type="number" name="sort_order" value="<?= (int)$editing['sort_order'] ?>" min="0"
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.pages.content_label') ?></label>
          <textarea name="content" rows="12"
                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-white font-mono text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors resize-y"><?= e($editing['content']) ?></textarea>
        </div>
        <div class="flex items-center justify-between bg-slate-900/50 border border-slate-800 rounded-lg px-4 py-3">
          <div>
            <p class="text-sm font-medium text-slate-300"><?= t('admin.pages.show_in_nav') ?></p>
            <p class="text-xs text-slate-500"><?= t('admin.pages.show_in_nav_desc') ?></p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="show_in_nav" value="0">
            <input type="checkbox" name="show_in_nav" value="1" class="sr-only peer" <?= $editing['show_in_nav'] ? 'checked' : '' ?>>
            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
          </label>
        </div>
        <div class="flex gap-3">
          <button type="submit" class="px-6 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-xl transition-colors shadow-lg shadow-brand/20">
            <?= t('admin.pages.save_btn') ?>
          </button>
          <a href="<?= url('admin/pages') ?>" class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-xl transition-colors">
            <?= t('admin.pages.cancel') ?>
          </a>
        </div>
      </div>
    </form>
  </div>
  <?php else: ?>
  <!-- Create form (collapsible) -->
  <div x-show="showForm" x-cloak x-transition class="glass rounded-2xl p-6 mb-6">
    <h2 class="font-semibold text-white mb-5"><?= t('admin.pages.new_title') ?></h2>
    <form method="POST" action="<?= url('admin/pages/create') ?>">
      <?= csrf_field() ?>
      <div class="space-y-4">
        <div class="grid sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.pages.title_label') ?></label>
            <input type="text" name="title" required
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                   placeholder="<?= e(t('admin.pages.title_ph')) ?>">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.pages.sort_order') ?></label>
            <input type="number" name="sort_order" value="0" min="0"
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.pages.content_label') ?></label>
          <textarea name="content" rows="10"
                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-white font-mono text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors resize-y"
                    placeholder="<?= e(t('admin.pages.content_ph')) ?>"></textarea>
        </div>
        <div class="flex items-center justify-between bg-slate-900/50 border border-slate-800 rounded-lg px-4 py-3">
          <div>
            <p class="text-sm font-medium text-slate-300"><?= t('admin.pages.show_in_nav') ?></p>
            <p class="text-xs text-slate-500"><?= t('admin.pages.show_in_nav_desc') ?></p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="show_in_nav" value="0">
            <input type="checkbox" name="show_in_nav" value="1" class="sr-only peer">
            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
          </label>
        </div>
        <div class="flex gap-3">
          <button type="submit" class="px-6 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-xl transition-colors shadow-lg shadow-brand/20">
            <?= t('admin.pages.create_btn') ?>
          </button>
          <button type="button" @click="showForm = false" class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-xl transition-colors">
            <?= t('admin.pages.cancel') ?>
          </button>
        </div>
      </div>
    </form>
  </div>
  <?php endif; ?>

  <!-- Pages list -->
  <?php if(empty($pages)): ?>
  <div class="glass rounded-2xl p-12 text-center">
    <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
      <svg class="w-7 h-7 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <p class="text-slate-400 font-medium mb-1"><?= t('admin.pages.none') ?></p>
    <p class="text-sm text-slate-600"><?= t('admin.pages.none_hint') ?></p>
  </div>
  <?php else: ?>
  <div class="space-y-2">
    <?php foreach($pages as $p): ?>
    <div class="glass rounded-xl px-5 py-4 flex items-center gap-4">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
          <p class="font-semibold text-white"><?= e($p['title']) ?></p>
          <?php if($p['show_in_nav']): ?>
          <span class="text-xs bg-brand/15 text-brand border border-brand/25 px-2 py-0.5 rounded-full"><?= t('admin.pages.in_nav') ?></span>
          <?php endif; ?>
        </div>
        <p class="text-xs text-slate-500 mt-0.5 font-mono">/page/<?= e($p['slug']) ?></p>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        <a href="<?= url('page/' . $p['slug']) ?>" target="_blank"
           class="px-3 py-1.5 text-xs text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
          <?= t('admin.pages.visit') ?>
        </a>
        <a href="<?= url('admin/pages/' . $p['id'] . '/edit') ?>"
           class="px-3 py-1.5 text-xs text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
          <?= t('admin.pages.edit') ?>
        </a>
        <form method="POST" action="<?= url('admin/pages/' . $p['id'] . '/delete') ?>" onsubmit="return confirm('<?= e(t('admin.pages.delete_confirm')) ?>')">
          <?= csrf_field() ?>
          <button type="submit" class="px-3 py-1.5 text-xs text-red-400 hover:text-red-300 bg-red-500/10 hover:bg-red-500/20 rounded-lg transition-colors">
            <?= t('admin.pages.delete') ?>
          </button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
