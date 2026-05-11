<?php $pageTitle = 'Ny tråd'; ?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
  <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
    <a href="<?= url('forum') ?>" class="hover:text-brand transition-colors">Forum</a>
    <span>›</span>
    <a href="<?= url('forum/category/' . urlencode($category['slug'])) ?>" class="hover:text-brand transition-colors"><?= e($category['name']) ?></a>
    <span>›</span>
    <span class="text-slate-300">Ny tråd</span>
  </nav>

  <h1 class="text-2xl font-bold text-white mb-7">Opret ny tråd</h1>

  <?php if($error ?? null): ?>
  <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-5 flex items-center gap-3 text-red-400 text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
    <?= e($error) ?>
  </div>
  <?php endif; ?>

  <div class="glass rounded-2xl p-6">
    <form method="POST" action="<?= url('forum/thread/create') ?>" class="space-y-5">
      <?= csrf_field() ?>
      <input type="hidden" name="category_id" value="<?= $category['id'] ?>">

      <div>
        <label class="block text-sm font-medium text-slate-300 mb-1.5">Titel <span class="text-red-400">*</span></label>
        <input type="text" name="title" required minlength="3" maxlength="100"
               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
               placeholder="Skriv en beskrivende titel...">
        <p class="text-xs text-slate-600 mt-1">3–100 tegn</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-300 mb-1.5">Indhold <span class="text-red-400">*</span></label>
        <textarea name="body" rows="10" required
                  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors resize-none"
                  placeholder="Beskriv dit emne i detaljer..."></textarea>
      </div>

      <div class="flex items-center justify-between pt-2">
        <a href="<?= url('forum/category/' . urlencode($category['slug'])) ?>"
           class="text-sm text-slate-400 hover:text-white transition-colors">← Annuller</a>
        <button type="submit" class="px-7 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-xl transition-colors shadow-lg shadow-brand/20">
          Opret tråd
        </button>
      </div>
    </form>
  </div>
</div>
