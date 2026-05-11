<?php $pageTitle = 'Indstillinger'; ?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10" x-data="{ tab: '<?= e($tab ?? 'profile') ?>' }">

  <!-- Header -->
  <div class="flex items-center gap-4 mb-8">
    <img src="<?= mcHead(e($user['username']), 56) ?>" class="w-14 h-14 rounded-2xl shadow-lg" alt="">
    <div>
      <h1 class="text-2xl font-bold text-white"><?= e($user['username']) ?></h1>
      <p class="text-sm text-slate-400"><?= e($user['email']) ?></p>
    </div>
  </div>

  <!-- Alerts -->
  <?php if($success ?? null): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 mb-5 flex items-center gap-3 text-emerald-400 text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <?= e($success) ?>
  </div>
  <?php endif; ?>
  <?php if($error ?? null): ?>
  <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-5 flex items-center gap-3 text-red-400 text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
    <?= e($error) ?>
  </div>
  <?php endif; ?>

  <div class="flex gap-6 flex-col sm:flex-row">

    <!-- Sidebar tabs -->
    <div class="sm:w-48 flex-shrink-0">
      <nav class="glass rounded-2xl p-2 flex sm:flex-col gap-1">
        <?php
        $tabs = [
          'profile'  => ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Profil'],
          'security' => ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'label' => 'Sikkerhed'],
          'danger'   => ['icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', 'label' => 'Slet konto'],
        ];
        foreach($tabs as $key => $t):
        ?>
        <button @click="tab = '<?= $key ?>'"
                class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-left w-full
                       <?= $key === 'danger' ? 'hover:bg-red-500/10 hover:text-red-400' : 'hover:bg-slate-800 hover:text-white' ?>"
                :class="tab === '<?= $key ?>' ? '<?= $key === 'danger' ? 'bg-red-500/15 text-red-400' : 'bg-brand/15 text-brand' ?>' : 'text-slate-400'">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $t['icon'] ?>"/>
          </svg>
          <?= $t['label'] ?>
        </button>
        <?php endforeach; ?>
      </nav>
    </div>

    <!-- Content -->
    <div class="flex-1 min-w-0">

      <!-- ── PROFIL TAB ── -->
      <div x-show="tab === 'profile'" class="space-y-4">

        <!-- Minecraft skin preview -->
        <div class="glass rounded-2xl p-6">
          <h2 class="font-semibold text-white mb-4">Brugernavn</h2>
          <div class="flex items-start gap-5 mb-5">
            <div class="text-center flex-shrink-0">
              <img src="<?= mcHead(e($user['username']), 80) ?>" class="w-20 h-20 rounded-xl shadow mb-1" alt="" id="skin-preview">
              <p class="text-xs text-slate-500">Minecraft skin</p>
            </div>
            <div class="flex-1">
              <p class="text-sm text-slate-400 mb-3">Dit brugernavn bruges til login og vises overalt på sitet. Brug dit Minecraft-navn for at vise din rigtige skin.</p>
              <form method="POST" action="<?= url('settings/username') ?>">
                <?= csrf_field() ?>
                <div class="flex gap-2">
                  <input type="text" name="username" value="<?= e($user['username']) ?>"
                         minlength="3" maxlength="30" required
                         class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                         oninput="document.getElementById('skin-preview').src='https://mc-heads.net/avatar/'+this.value+'/80'">
                  <button type="submit" class="px-5 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors whitespace-nowrap">
                    Gem
                  </button>
                </div>
                <p class="text-xs text-slate-600 mt-1.5">3–30 tegn, bogstaver, tal, _ og - er tilladt.</p>
              </form>
            </div>
          </div>
        </div>

        <!-- Stats (read-only) -->
        <div class="glass rounded-2xl p-5">
          <h2 class="font-semibold text-white mb-4">Statistik</h2>
          <div class="grid grid-cols-3 gap-3">
            <?php
              $postCount   = \App\Models\ForumPost::count('user_id = ?',   [$user['id']]);
              $threadCount = \App\Models\ForumThread::count('user_id = ?', [$user['id']]);
            ?>
            <div class="bg-slate-900/50 rounded-xl p-3 text-center">
              <p class="text-xl font-bold text-white"><?= number_format($threadCount) ?></p>
              <p class="text-xs text-slate-500 mt-0.5">Tråde</p>
            </div>
            <div class="bg-slate-900/50 rounded-xl p-3 text-center">
              <p class="text-xl font-bold text-white"><?= number_format($postCount) ?></p>
              <p class="text-xs text-slate-500 mt-0.5">Indlæg</p>
            </div>
            <div class="bg-slate-900/50 rounded-xl p-3 text-center">
              <p class="text-sm font-semibold text-white"><?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
              <p class="text-xs text-slate-500 mt-0.5">Tilmeldt</p>
            </div>
          </div>
        </div>

      </div>

      <!-- ── SIKKERHED TAB ── -->
      <div x-show="tab === 'security'" class="space-y-4">

        <!-- Change email -->
        <div class="glass rounded-2xl p-6">
          <h2 class="font-semibold text-white mb-1">Skift email</h2>
          <p class="text-sm text-slate-400 mb-4">Din email bruges til login. Kræver din nuværende adgangskode.</p>
          <form method="POST" action="<?= url('settings/email') ?>" class="space-y-3">
            <?= csrf_field() ?>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5">Ny email</label>
              <input type="email" name="email" value="<?= e($user['email']) ?>" required
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5">Bekræft med adgangskode</label>
              <input type="password" name="current_password" required placeholder="Din nuværende adgangskode"
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div class="flex justify-end">
              <button type="submit" class="px-5 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">
                Opdater email
              </button>
            </div>
          </form>
        </div>

        <!-- Change password -->
        <div class="glass rounded-2xl p-6">
          <h2 class="font-semibold text-white mb-1">Skift adgangskode</h2>
          <p class="text-sm text-slate-400 mb-4">Vælg en stærk adgangskode på mindst 8 tegn.</p>
          <form method="POST" action="<?= url('settings/password') ?>" class="space-y-3">
            <?= csrf_field() ?>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5">Nuværende adgangskode</label>
              <input type="password" name="current_password" required
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5">Ny adgangskode</label>
              <input type="password" name="new_password" required minlength="8" placeholder="Min. 8 tegn"
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5">Gentag ny adgangskode</label>
              <input type="password" name="confirm_password" required minlength="8" placeholder="Gentag adgangskode"
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div class="flex justify-end">
              <button type="submit" class="px-5 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">
                Skift adgangskode
              </button>
            </div>
          </form>
        </div>

      </div>

      <!-- ── SLET KONTO TAB ── -->
      <div x-show="tab === 'danger'">
        <div class="glass rounded-2xl p-6 border border-red-500/20">
          <div class="flex items-start gap-4 mb-6">
            <div class="w-10 h-10 bg-red-500/15 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
              <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
              <h2 class="font-semibold text-white mb-1">Slet konto permanent</h2>
              <p class="text-sm text-slate-400">Dette sletter din konto, alle dine tråde og indlæg for altid. <strong class="text-white">Handlingen kan ikke fortrydes.</strong></p>
            </div>
          </div>

          <form method="POST" action="<?= url('settings/delete-account') ?>" class="space-y-4"
                x-data="{ confirmed: false }"
                onsubmit="return confirm('Er du 100% sikker? Din konto og alt indhold slettes permanent.')">
            <?= csrf_field() ?>

            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1.5">
                Skriv <span class="text-red-400 font-mono">slet min konto</span> for at bekræfte
              </label>
              <input type="text" name="confirm_text" required autocomplete="off"
                     placeholder="slet min konto"
                     x-on:input="confirmed = $event.target.value.toLowerCase() === 'slet min konto'"
                     class="w-full bg-slate-900 border border-red-500/30 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/30 transition-colors">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1.5">Bekræft med din adgangskode</label>
              <input type="password" name="confirm_password" required
                     class="w-full bg-slate-900 border border-red-500/30 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/30 transition-colors">
            </div>

            <button type="submit"
                    :disabled="!confirmed"
                    class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
              Slet min konto permanent
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>
