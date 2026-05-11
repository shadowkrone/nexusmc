<div class="space-y-6">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-white">Ansøgningsformularer</h1>
      <p class="text-sm text-slate-400 mt-0.5">Opret og rediger formularer brugere kan ansøge til</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="<?= url('admin/applications') ?>" class="text-sm text-slate-400 hover:text-white transition-colors">
        ← Ansøgninger
      </a>
      <a href="<?= url('admin/applications/forms/new') ?>"
         class="px-4 py-2 bg-brand hover:bg-brand-dark text-white text-sm font-semibold rounded-lg transition-colors">
        Ny formular
      </a>
    </div>
  </div>

  <?php if(empty($forms)): ?>
  <div class="glass rounded-2xl p-10 text-center text-slate-500">
    <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <p>Ingen formularer endnu.</p>
    <a href="<?= url('admin/applications/forms/new') ?>" class="inline-block mt-3 text-brand hover:text-brand-light text-sm transition-colors">
      Opret den første →
    </a>
  </div>
  <?php else: ?>
  <div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-slate-800">
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Formular</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide hidden sm:table-cell">Slug</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Ansøgninger</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
          <th class="px-5 py-3.5"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-800/50">
        <?php foreach($forms as $form): ?>
        <tr class="hover:bg-slate-800/30 transition-colors">
          <td class="px-5 py-3.5">
            <p class="font-medium text-white"><?= e($form['name']) ?></p>
            <?php if($form['description']): ?>
            <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs"><?= e($form['description']) ?></p>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3.5 hidden sm:table-cell">
            <code class="text-xs text-slate-400 bg-slate-800 px-2 py-0.5 rounded"><?= e($form['slug']) ?></code>
          </td>
          <td class="px-5 py-3.5">
            <span class="text-white font-medium"><?= $form['application_count'] ?></span>
            <?php if($form['pending_count'] > 0): ?>
            <span class="text-xs text-amber-400 ml-1">(<?= $form['pending_count'] ?> afventer)</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3.5">
            <?php if($form['active']): ?>
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400">Aktiv</span>
            <?php else: ?>
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-700 text-slate-400">Inaktiv</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3.5 text-right">
            <div class="flex items-center justify-end gap-2">
              <a href="<?= url('admin/applications/forms/' . $form['id'] . '/edit') ?>"
                 class="text-xs text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 rounded-lg px-3 py-1.5 transition-colors">
                Rediger
              </a>
              <form method="POST" action="<?= url('admin/applications/forms/' . $form['id'] . '/delete') ?>"
                    onsubmit="return confirm('Slet formularen og alle tilhørende ansøgninger?')">
                <?= csrf_field() ?>
                <button type="submit" class="text-xs text-red-400 hover:text-red-300 border border-red-500/30 hover:border-red-400/50 rounded-lg px-3 py-1.5 transition-colors">
                  Slet
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

</div>
