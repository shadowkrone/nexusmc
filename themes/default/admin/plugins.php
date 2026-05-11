<div class="max-w-4xl">
  <div class="flex items-center justify-between mb-7">
    <div>
      <h1 class="text-2xl font-bold text-white">Plugins</h1>
      <p class="text-sm text-slate-400 mt-1">Placer plugins i <code class="bg-slate-800 px-1.5 py-0.5 rounded text-xs">/plugins/&lt;navn&gt;/</code> mappen</p>
    </div>
  </div>

  <?php if(empty($plugins)): ?>
  <div class="glass rounded-2xl p-12 text-center">
    <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
      <svg class="w-7 h-7 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
    </div>
    <p class="text-slate-400 font-medium mb-2">Ingen plugins installeret</p>
    <p class="text-sm text-slate-600">Hent plugins og placer dem i <code class="bg-slate-800 px-1.5 py-0.5 rounded">/plugins/</code> mappen.</p>
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
        <p class="text-xs text-slate-400 mt-0.5"><?= e($info['description'] ?? '') ?> <?php if($v = $info['version'] ?? null): ?> · v<?= e($v) ?><?php endif; ?></p>
      </div>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer" <?= $info['enabled'] ? 'checked' : '' ?>
               onchange="togglePlugin('<?= e($name) ?>', this.checked)">
        <div class="w-11 h-6 bg-slate-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
      </label>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<script>
async function togglePlugin(name, enabled) {
  await fetch(`<?= url('admin/plugins') ?>/${name}/toggle`, {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `_csrf=<?= csrf() ?>`
  });
}
</script>
