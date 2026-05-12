<div class="max-w-3xl">
  <h1 class="text-2xl font-bold text-white mb-7"><?= t('admin.settings.title') ?></h1>

  <form method="POST" action="<?= url('admin/settings') ?>" class="space-y-6">
    <?= csrf_field() ?>

    <!-- General -->
    <div class="glass rounded-2xl p-6 space-y-5">
      <h2 class="font-semibold text-white border-b border-slate-800 pb-3"><?= t('admin.settings.general') ?></h2>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.settings.site_name') ?></label>
          <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.settings.site_description') ?></label>
          <input type="text" name="site_description" value="<?= e($settings['site_description'] ?? '') ?>"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.settings.logo_url') ?></label>
          <input type="url" name="site_logo" value="<?= e($settings['site_logo'] ?? '') ?>"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="https://...">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.settings.language') ?></label>
          <select name="language"
                  class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            <option value="en" <?= ($settings['language'] ?? 'en') === 'en' ? 'selected' : '' ?>><?= t('admin.settings.language_en') ?></option>
            <option value="da" <?= ($settings['language'] ?? 'en') === 'da' ? 'selected' : '' ?>><?= t('admin.settings.language_da') ?></option>
          </select>
        </div>
        <div class="flex items-center justify-between bg-slate-900/50 border border-slate-800 rounded-lg px-4 py-3 sm:col-span-2">
          <div>
            <p class="text-sm font-medium text-slate-300"><?= t('admin.settings.registration_open') ?></p>
            <p class="text-xs text-slate-500"><?= t('admin.settings.allow_new_users') ?></p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="registration_open" value="0">
            <input type="checkbox" name="registration_open" value="1" class="sr-only peer" <?= ($settings['registration_open'] ?? '1') === '1' ? 'checked' : '' ?>>
            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
          </label>
        </div>
      </div>
    </div>

    <!-- Minecraft Server -->
    <div class="glass rounded-2xl p-6 space-y-5">
      <h2 class="font-semibold text-white border-b border-slate-800 pb-3"><?= t('admin.settings.minecraft_server') ?></h2>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.settings.server_ip') ?></label>
          <input type="text" name="server_ip" value="<?= e($settings['server_ip'] ?? '') ?>"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="play.yourserver.com">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.settings.server_port') ?></label>
          <input type="number" name="server_port" value="<?= e($settings['server_port'] ?? '25565') ?>" min="1" max="65535"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
        </div>
      </div>
    </div>

    <!-- Links -->
    <div class="glass rounded-2xl p-6 space-y-5">
      <h2 class="font-semibold text-white border-b border-slate-800 pb-3"><?= t('admin.settings.links') ?></h2>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.settings.discord_url') ?></label>
          <input type="url" name="discord_url" value="<?= e($settings['discord_url'] ?? '') ?>"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="https://discord.gg/...">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5"><?= t('admin.settings.shop_url') ?></label>
          <input type="url" name="store_url" value="<?= e($settings['store_url'] ?? '') ?>"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                 placeholder="https://...">
        </div>
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit" class="px-7 py-3 bg-brand hover:bg-brand-dark text-white font-semibold rounded-xl transition-colors shadow-lg shadow-brand/20">
        <?= t('admin.settings.save') ?>
      </button>
    </div>
  </form>
</div>
