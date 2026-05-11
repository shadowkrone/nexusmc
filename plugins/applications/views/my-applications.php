<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">

  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl font-bold text-white">Mine ansøgninger</h1>
      <p class="text-sm text-slate-400 mt-1">Status på dine indsendte ansøgninger</p>
    </div>
    <a href="<?= url('applications') ?>" class="text-sm text-brand hover:text-brand-light transition-colors">
      Se formularer →
    </a>
  </div>

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

  <?php if(empty($applications)): ?>
  <div class="glass rounded-2xl p-10 text-center text-slate-500">
    <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <p>Du har ikke sendt nogen ansøgninger endnu.</p>
    <a href="<?= url('applications') ?>" class="inline-block mt-4 text-brand hover:text-brand-light text-sm transition-colors">
      Se åbne formularer →
    </a>
  </div>
  <?php else: ?>
  <div class="glass rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-slate-800">
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Formular</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Sendt</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-800/50">
        <?php foreach($applications as $app): ?>
        <?php
          $statusMap = [
            'pending'  => ['Afventer behandling', 'bg-amber-500/15 text-amber-400',  'Behandles snart af vores team.'],
            'accepted' => ['Godkendt',            'bg-emerald-500/15 text-emerald-400', 'Tillykke! Din ansøgning er godkendt.'],
            'denied'   => ['Afvist',              'bg-red-500/15 text-red-400',      'Din ansøgning blev desværre afvist.'],
          ];
          [$label, $cls, $msg] = $statusMap[$app['status']] ?? ['Ukendt', 'bg-slate-700 text-slate-400', ''];
        ?>
        <tr class="hover:bg-slate-800/30 transition-colors">
          <td class="px-5 py-4">
            <span class="font-medium text-white"><?= e($app['form_name']) ?></span>
          </td>
          <td class="px-5 py-4 text-slate-400">
            <?= date('d/m/Y', strtotime($app['created_at'])) ?>
          </td>
          <td class="px-5 py-4">
            <div>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $cls ?>">
                <?= $label ?>
              </span>
              <p class="text-xs text-slate-500 mt-1"><?= $msg ?></p>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

</div>
