<div class="max-w-6xl">
  <h1 class="text-2xl font-bold text-white mb-7">Dashboard</h1>

  <!-- Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="glass rounded-2xl p-6 flex items-center gap-4">
      <div class="w-12 h-12 bg-blue-500/15 border border-blue-500/25 rounded-xl flex items-center justify-center">
        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
      </div>
      <div>
        <p class="text-3xl font-extrabold text-white"><?= number_format($stats['users']) ?></p>
        <p class="text-sm text-slate-400">Brugere</p>
      </div>
    </div>
    <div class="glass rounded-2xl p-6 flex items-center gap-4">
      <div class="w-12 h-12 bg-brand/15 border border-brand/25 rounded-xl flex items-center justify-center">
        <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
      </div>
      <div>
        <p class="text-3xl font-extrabold text-white"><?= number_format($stats['threads']) ?></p>
        <p class="text-sm text-slate-400">Tråde</p>
      </div>
    </div>
    <div class="glass rounded-2xl p-6 flex items-center gap-4">
      <div class="w-12 h-12 bg-purple-500/15 border border-purple-500/25 rounded-xl flex items-center justify-center">
        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
      </div>
      <div>
        <p class="text-3xl font-extrabold text-white"><?= number_format($stats['posts']) ?></p>
        <p class="text-sm text-slate-400">Indlæg</p>
      </div>
    </div>
  </div>

  <!-- Recent users -->
  <div class="glass rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
      <h2 class="font-semibold text-white">Nye brugere</h2>
      <a href="<?= url('admin/users') ?>" class="text-sm text-brand hover:text-brand-light">Se alle →</a>
    </div>
    <div class="divide-y divide-slate-800/50">
      <?php foreach($recentUsers as $u): ?>
      <div class="flex items-center gap-3 px-5 py-3">
        <img src="<?= mcHead(e($u['username']), 32) ?>" class="w-8 h-8 rounded-lg flex-shrink-0" alt="">
        <div class="flex-1 min-w-0">
          <p class="font-medium text-white text-sm"><?= e($u['username']) ?></p>
          <p class="text-xs text-slate-500"><?= e($u['email']) ?></p>
        </div>
        <div class="flex items-center gap-3">
          <span class="text-xs px-2 py-0.5 rounded-full <?= \App\Models\User::roleBadgeClass($u['role']) ?>">
            <?= \App\Models\User::roleLabel($u['role']) ?>
          </span>
          <span class="text-xs text-slate-500"><?= timeAgo($u['created_at']) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
