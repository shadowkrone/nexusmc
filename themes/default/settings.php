<?php
$pageTitle = t('settings.title');
$confirmValue = t('settings.danger_confirm_value');
?>
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
          'profile'  => ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => t('settings.profile')],
          'security' => ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'label' => t('settings.security')],
          'danger'   => ['icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', 'label' => t('settings.delete_account_tab')],
        ];
        foreach($tabs as $key => $t_):
        ?>
        <button @click="tab = '<?= $key ?>'"
                class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-left w-full
                       <?= $key === 'danger' ? 'hover:bg-red-500/10 hover:text-red-400' : 'hover:bg-slate-800 hover:text-white' ?>"
                :class="tab === '<?= $key ?>' ? '<?= $key === 'danger' ? 'bg-red-500/15 text-red-400' : 'bg-brand/15 text-brand' ?>' : 'text-slate-400'">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $t_['icon'] ?>"/>
          </svg>
          <?= $t_['label'] ?>
        </button>
        <?php endforeach; ?>
      </nav>
    </div>

    <!-- Content -->
    <div class="flex-1 min-w-0">

      <!-- ── PROFILE TAB ── -->
      <div x-show="tab === 'profile'" class="space-y-4">

        <!-- Minecraft skin preview -->
        <div class="glass rounded-2xl p-6">
          <h2 class="font-semibold text-white mb-4"><?= t('settings.username_title') ?></h2>
          <div class="flex items-start gap-5 mb-5">
            <div class="text-center flex-shrink-0">
              <img src="<?= mcHead(e($user['username']), 80) ?>" class="w-20 h-20 rounded-xl shadow mb-1" alt="" id="skin-preview">
              <p class="text-xs text-slate-500"><?= t('settings.minecraft_skin') ?></p>
            </div>
            <div class="flex-1">
              <p class="text-sm text-slate-400 mb-3"><?= t('settings.username_desc') ?></p>
              <form method="POST" action="<?= url('settings/username') ?>">
                <?= csrf_field() ?>
                <div class="flex gap-2">
                  <input type="text" name="username" value="<?= e($user['username']) ?>"
                         minlength="3" maxlength="30" required
                         class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                         oninput="document.getElementById('skin-preview').src='https://mc-heads.net/avatar/'+this.value+'/80'">
                  <button type="submit" class="px-5 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors whitespace-nowrap">
                    <?= t('settings.save') ?>
                  </button>
                </div>
                <p class="text-xs text-slate-600 mt-1.5"><?= t('settings.username_hint') ?></p>
              </form>
            </div>
          </div>
        </div>

        <!-- Stats (read-only) -->
        <div class="glass rounded-2xl p-5">
          <h2 class="font-semibold text-white mb-4"><?= t('settings.stats') ?></h2>
          <div class="grid grid-cols-3 gap-3">
            <?php
              $postCount   = \App\Models\ForumPost::count('user_id = ?',   [$user['id']]);
              $threadCount = \App\Models\ForumThread::count('user_id = ?', [$user['id']]);
            ?>
            <div class="bg-slate-900/50 rounded-xl p-3 text-center">
              <p class="text-xl font-bold text-white"><?= number_format($threadCount) ?></p>
              <p class="text-xs text-slate-500 mt-0.5"><?= t('settings.threads') ?></p>
            </div>
            <div class="bg-slate-900/50 rounded-xl p-3 text-center">
              <p class="text-xl font-bold text-white"><?= number_format($postCount) ?></p>
              <p class="text-xs text-slate-500 mt-0.5"><?= t('settings.posts') ?></p>
            </div>
            <div class="bg-slate-900/50 rounded-xl p-3 text-center">
              <p class="text-sm font-semibold text-white"><?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
              <p class="text-xs text-slate-500 mt-0.5"><?= t('settings.joined') ?></p>
            </div>
          </div>
        </div>

      </div>

      <!-- ── SECURITY TAB ── -->
      <div x-show="tab === 'security'" class="space-y-4">

        <!-- Change email -->
        <div class="glass rounded-2xl p-6">
          <h2 class="font-semibold text-white mb-1"><?= t('settings.change_email') ?></h2>
          <p class="text-sm text-slate-400 mb-4"><?= t('settings.change_email_desc') ?></p>
          <form method="POST" action="<?= url('settings/email') ?>" class="space-y-3">
            <?= csrf_field() ?>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5"><?= t('settings.new_email') ?></label>
              <input type="email" name="email" value="<?= e($user['email']) ?>" required
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5"><?= t('settings.confirm_with_password') ?></label>
              <input type="password" name="current_password" required placeholder="<?= t('settings.current_password_ph') ?>"
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div class="flex justify-end">
              <button type="submit" class="px-5 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">
                <?= t('settings.update_email') ?>
              </button>
            </div>
          </form>
        </div>

        <!-- Change password -->
        <div class="glass rounded-2xl p-6">
          <h2 class="font-semibold text-white mb-1"><?= t('settings.change_password') ?></h2>
          <p class="text-sm text-slate-400 mb-4"><?= t('settings.change_password_desc') ?></p>
          <form method="POST" action="<?= url('settings/password') ?>" class="space-y-3">
            <?= csrf_field() ?>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5"><?= t('settings.current_password') ?></label>
              <input type="password" name="current_password" required
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5"><?= t('settings.new_password') ?></label>
              <input type="password" name="new_password" required minlength="8" placeholder="<?= t('auth.min_8_chars') ?>"
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-400 mb-1.5"><?= t('settings.repeat_password') ?></label>
              <input type="password" name="confirm_password" required minlength="8" placeholder="<?= t('settings.repeat_password_ph') ?>"
                     class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            </div>
            <div class="flex justify-end">
              <button type="submit" class="px-5 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">
                <?= t('settings.change_password_btn') ?>
              </button>
            </div>
          </form>
        </div>

      </div>

      <!-- ── DELETE ACCOUNT TAB ── -->
      <div x-show="tab === 'danger'">
        <div class="glass rounded-2xl p-6 border border-red-500/20">
          <div class="flex items-start gap-4 mb-6">
            <div class="w-10 h-10 bg-red-500/15 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
              <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
              <h2 class="font-semibold text-white mb-1"><?= t('settings.danger_title') ?></h2>
              <p class="text-sm text-slate-400"><?= t('settings.danger_desc') ?> <strong class="text-white"><?= t('settings.danger_cannot_undo') ?></strong></p>
            </div>
          </div>

          <form method="POST" action="<?= url('settings/delete-account') ?>" class="space-y-4"
                x-data="{ confirmed: false }"
                onsubmit="return confirm('<?= e(t('settings.danger_js_confirm')) ?>')">
            <?= csrf_field() ?>

            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1.5">
                <?= t('settings.danger_confirm_label') ?>
              </label>
              <input type="text" name="confirm_text" required autocomplete="off"
                     placeholder="<?= e($confirmValue) ?>"
                     x-on:input="confirmed = $event.target.value.toLowerCase() === '<?= e($confirmValue) ?>'"
                     class="w-full bg-slate-900 border border-red-500/30 rounded-lg px-4 py-2.5 text-white placeholder-slate-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/30 transition-colors">
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('settings.danger_password_label') ?></label>
              <input type="password" name="confirm_password" required
                     class="w-full bg-slate-900 border border-red-500/30 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/30 transition-colors">
            </div>

            <button type="submit"
                    :disabled="!confirmed"
                    class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
              <?= t('settings.danger_btn') ?>
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>
