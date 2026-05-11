<?php $pageTitle = 'Indstillinger'; ?>
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
  <h1 class="text-2xl font-bold text-white mb-7">Kontoindstillinger</h1>

  <?php if($success ?? null): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 mb-5 flex items-center gap-3 text-emerald-400 text-sm">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <?= e($success) ?>
  </div>
  <?php endif; ?>

  <div class="glass rounded-2xl p-6">
    <!-- Profile preview -->
    <div class="flex items-center gap-4 pb-5 border-b border-slate-800 mb-5">
      <img src="<?= mcHead(e($user['username']), 48) ?>" class="w-12 h-12 rounded-xl" alt="">
      <div>
        <p class="font-semibold text-white"><?= e($user['username']) ?></p>
        <p class="text-sm text-slate-400"><?= e($user['email']) ?></p>
      </div>
    </div>

    <form method="POST" action="<?= url('settings') ?>" class="space-y-5">
      <?= csrf_field() ?>
      <div>
        <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
        <input type="email" name="email" value="<?= e($user['email']) ?>"
               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
      </div>
      <div class="border-t border-slate-800 pt-5">
        <h3 class="text-sm font-semibold text-slate-300 mb-3">Skift adgangskode</h3>
        <div class="space-y-3">
          <input type="password" name="password" placeholder="Ny adgangskode (lad stå tom for ingen ændring)"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
        </div>
      </div>
      <div class="flex justify-end pt-2">
        <button type="submit" class="px-6 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">
          Gem ændringer
        </button>
      </div>
    </form>
  </div>
</div>
