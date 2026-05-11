<div class="space-y-6 max-w-3xl">

  <!-- Breadcrumb -->
  <div class="flex items-center gap-2 text-sm text-slate-400">
    <a href="<?= url('admin/applications') ?>" class="hover:text-white transition-colors">Ansøgninger</a>
    <span>/</span>
    <span class="text-white">#<?= $application['id'] ?> — <?= e($application['username']) ?></span>
  </div>

  <!-- Applicant + status bar -->
  <div class="glass rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <img src="<?= mcHead(e($application['username']), 48) ?>" class="w-12 h-12 rounded-xl" alt="">
      <div>
        <p class="font-semibold text-white"><?= e($application['username']) ?></p>
        <p class="text-sm text-slate-400"><?= e($application['form_name']) ?> · <?= date('d/m/Y H:i', strtotime($application['created_at'])) ?></p>
      </div>
    </div>

    <!-- Status buttons -->
    <div class="flex items-center gap-2 flex-wrap">
      <?php
        $statuses = [
          'pending'  => ['Afventer', 'border-amber-500/40 text-amber-400 hover:bg-amber-500/10'],
          'accepted' => ['Godkend',  'border-emerald-500/40 text-emerald-400 hover:bg-emerald-500/10'],
          'denied'   => ['Afvis',    'border-red-500/40 text-red-400 hover:bg-red-500/10'],
        ];
        foreach($statuses as $val => [$btnLabel, $btnCls]):
          $isActive = $application['status'] === $val;
          $activeCls = match($val) {
            'pending'  => 'bg-amber-500/15 border-amber-500/40 text-amber-400',
            'accepted' => 'bg-emerald-500/15 border-emerald-500/40 text-emerald-400',
            'denied'   => 'bg-red-500/15 border-red-500/40 text-red-400',
          };
      ?>
      <form method="POST" action="<?= url('admin/applications/' . $application['id'] . '/status') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="status" value="<?= $val ?>">
        <button type="submit"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-colors <?= $isActive ? $activeCls : 'border-slate-700 text-slate-400 hover:border-slate-500 hover:text-white' ?>">
          <?= $btnLabel ?>
        </button>
      </form>
      <?php endforeach; ?>

      <!-- Delete -->
      <form method="POST" action="<?= url('admin/applications/' . $application['id'] . '/delete') ?>"
            onsubmit="return confirm('Slet denne ansøgning permanent?')">
        <?= csrf_field() ?>
        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-red-500/30 text-red-400 hover:bg-red-500/10 transition-colors">
          Slet
        </button>
      </form>
    </div>
  </div>

  <!-- Q&A -->
  <div class="glass rounded-2xl p-6 space-y-5">
    <h2 class="font-semibold text-white">Svar</h2>
    <?php if(empty($qa)): ?>
    <p class="text-slate-500 text-sm">Ingen spørgsmål i denne formular.</p>
    <?php else: ?>
    <?php foreach($qa as $item): ?>
    <div>
      <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5"><?= e($item['label']) ?></p>
      <div class="bg-slate-900/60 rounded-xl px-4 py-3 text-sm text-slate-200 whitespace-pre-wrap leading-relaxed"><?= e($item['answer']) ?: '<span class="text-slate-600 italic">Ikke besvaret</span>' ?></div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Notes -->
  <div class="glass rounded-2xl p-6 space-y-4">
    <h2 class="font-semibold text-white">Interne noter</h2>

    <?php if(empty($notes)): ?>
    <p class="text-slate-500 text-sm">Ingen noter endnu.</p>
    <?php else: ?>
    <div class="space-y-3">
      <?php foreach($notes as $note): ?>
      <div class="bg-slate-900/50 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-1.5">
          <img src="<?= mcHead(e($note['admin_username']), 20) ?>" class="w-5 h-5 rounded" alt="">
          <span class="text-xs font-medium text-slate-300"><?= e($note['admin_username']) ?></span>
          <span class="text-xs text-slate-600">·</span>
          <span class="text-xs text-slate-500"><?= date('d/m/Y H:i', strtotime($note['created_at'])) ?></span>
        </div>
        <p class="text-sm text-slate-300 whitespace-pre-wrap"><?= e($note['body']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Add note -->
    <form method="POST" action="<?= url('admin/applications/' . $application['id'] . '/note') ?>" class="space-y-3">
      <?= csrf_field() ?>
      <textarea name="body" rows="3" required placeholder="Skriv en intern note..."
                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors resize-none text-sm"></textarea>
      <div class="flex justify-end">
        <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors">
          Tilføj note
        </button>
      </div>
    </form>
  </div>

</div>
