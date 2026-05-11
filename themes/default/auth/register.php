<?php $pageTitle = 'Opret konto'; ?>
<div class="min-h-[80vh] flex items-center justify-center px-4 py-16">
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <div class="w-16 h-16 gradient-brand rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-brand/20">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
      </div>
      <h1 class="text-3xl font-bold text-white">Opret konto</h1>
      <p class="text-slate-400 mt-2">Gratis og tager kun et minut</p>
    </div>

    <?php if($error ?? null): ?>
    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-5 flex items-center gap-3 text-red-400">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
      <span class="text-sm"><?= e($error) ?></span>
    </div>
    <?php endif; ?>

    <div class="glass rounded-2xl p-7">
      <form method="POST" action="<?= url('register') ?>" class="space-y-5" x-data="{username:'',checking:false}">
        <?= csrf_field() ?>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">Brugernavn</label>
          <div class="relative">
            <input type="text" name="username" x-model="username" required minlength="3" maxlength="20"
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 pr-10 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                   placeholder="CoolGamer123">
            <div x-show="username.length >= 3" class="absolute right-3 top-3">
              <img :src="`https://mc-heads.net/avatar/${username}/20`" class="w-5 h-5 rounded" alt="">
            </div>
          </div>
          <p class="text-xs text-slate-600 mt-1">3–20 tegn. Dit Minecraft brugernavn for at vise din skin.</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
          <input type="email" name="email" required autocomplete="email"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="din@email.dk">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">Adgangskode</label>
          <input type="password" name="password" required minlength="8"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="Min. 8 tegn">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">Bekræft adgangskode</label>
          <input type="password" name="password2" required
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="••••••••">
        </div>
        <button type="submit" class="w-full py-3 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors shadow-lg shadow-brand/20">
          Opret gratis konto
        </button>
      </form>
    </div>

    <p class="text-center text-sm text-slate-500 mt-5">
      Har du en konto?
      <a href="<?= url('login') ?>" class="text-brand hover:text-brand-light font-medium transition-colors">Log ind her</a>
    </p>
  </div>
</div>
