<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">

  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl font-bold text-white">Ansøgninger</h1>
      <p class="text-sm text-slate-400 mt-1">Åbne formularer du kan ansøge til</p>
    </div>
    <?php if(auth()->check()): ?>
    <a href="<?= url('my-applications') ?>" class="text-sm text-brand hover:text-brand-light transition-colors">
      Mine ansøgninger →
    </a>
    <?php endif; ?>
  </div>

  <?php if(empty($forms)): ?>
  <div class="glass rounded-2xl p-10 text-center text-slate-500">
    <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <p>Der er ingen åbne ansøgningsformularer i øjeblikket.</p>
  </div>
  <?php else: ?>
  <div class="space-y-4">
    <?php foreach($forms as $form): ?>
    <?php $existing = $applied[$form['id']] ?? null; ?>
    <div class="glass rounded-2xl p-6 flex items-start gap-5">
      <div class="w-11 h-11 bg-brand/15 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
        <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </div>
      <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="font-semibold text-white"><?= e($form['name']) ?></h2>
            <?php if($form['description']): ?>
            <p class="text-sm text-slate-400 mt-1"><?= e($form['description']) ?></p>
            <?php endif; ?>
          </div>
          <?php if($existing): ?>
            <?php
              $statusMap = [
                'pending'  => ['Afventer', 'bg-amber-500/15 text-amber-400'],
                'accepted' => ['Godkendt', 'bg-emerald-500/15 text-emerald-400'],
                'denied'   => ['Afvist',   'bg-red-500/15 text-red-400'],
              ];
              [$label, $cls] = $statusMap[$existing['status']] ?? ['Ukendt', 'bg-slate-700 text-slate-400'];
            ?>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0 <?= $cls ?>">
              <?= $label ?>
            </span>
          <?php endif; ?>
        </div>
        <div class="mt-4">
          <?php if($existing): ?>
            <a href="<?= url('my-applications') ?>" class="text-sm text-slate-400 hover:text-white transition-colors">
              Se din ansøgning →
            </a>
          <?php elseif(auth()->check()): ?>
            <a href="<?= url('apply/' . e($form['slug'])) ?>"
               class="inline-flex items-center gap-2 px-4 py-2 bg-brand hover:bg-brand-dark text-white text-sm font-semibold rounded-lg transition-colors">
              Ansøg nu
            </a>
          <?php else: ?>
            <a href="<?= url('login') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors">
              Log ind for at ansøge
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>
