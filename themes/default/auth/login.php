<?php $pageTitle = 'Log ind'; ?>
<div class="min-h-[80vh] flex items-center justify-center px-4 py-16">
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <div class="w-16 h-16 gradient-brand rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-brand/20">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
      </div>
      <h1 class="text-3xl font-bold text-white">Log ind</h1>
      <p class="text-slate-400 mt-2">Velkommen tilbage!</p>
    </div>

    <?php if($error ?? null): ?>
    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-5 flex items-center gap-3 text-red-400">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
      <span class="text-sm"><?= e($error) ?></span>
    </div>
    <?php endif; ?>

    <div class="glass rounded-2xl p-7">
      <form method="POST" action="<?= url('login') ?>" class="space-y-5">
        <?= csrf_field() ?>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
          <input type="email" name="email" required autocomplete="email"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="din@email.dk">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">Adgangskode</label>
          <div class="relative" x-data="{show:false}">
            <input :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 pr-11 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                   placeholder="••••••••">
            <button type="button" @click="show=!show" class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-300">
              <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>
        <button type="submit" class="w-full py-3 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors shadow-lg shadow-brand/20">
          Log ind
        </button>
      </form>
    </div>

    <p class="text-center text-sm text-slate-500 mt-5">
      Ingen konto?
      <a href="<?= url('register') ?>" class="text-brand hover:text-brand-light font-medium transition-colors">Opret dig her</a>
    </p>
  </div>
</div>
