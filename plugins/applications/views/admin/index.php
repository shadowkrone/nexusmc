<div class="space-y-6">

  <!-- Header -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-xl font-bold text-white">Ansøgninger</h1>
      <p class="text-sm text-slate-400 mt-0.5">Administrer indkomne ansøgninger</p>
    </div>
    <a href="<?= url('admin/applications/forms') ?>"
       class="px-4 py-2 bg-brand hover:bg-brand-dark text-white text-sm font-semibold rounded-lg transition-colors">
      Administrer formularer
    </a>
  </div>

  <!-- Stats -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <?php
      $stats = [
        ['label' => 'I alt',      'value' => $counts['total'],    'cls' => 'text-white'],
        ['label' => 'Afventer',   'value' => $counts['pending'],  'cls' => 'text-amber-400'],
        ['label' => 'Godkendt',   'value' => $counts['accepted'], 'cls' => 'text-emerald-400'],
        ['label' => 'Afvist',     'value' => $counts['denied'],   'cls' => 'text-red-400'],
      ];
      foreach($stats as $s):
    ?>
    <div class="glass rounded-xl p-4 text-center">
      <p class="text-2xl font-bold <?= $s['cls'] ?>"><?= $s['value'] ?></p>
      <p class="text-xs text-slate-500 mt-0.5"><?= $s['label'] ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Filters -->
  <div class="glass rounded-2xl p-4">
    <form method="GET" action="<?= url('admin/applications') ?>" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Formular</label>
        <select name="form" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-brand transition-colors">
          <option value="">Alle formularer</option>
          <?php foreach($forms as $f): ?>
          <option value="<?= $f['id'] ?>" <?= $filterForm === $f['id'] ? 'selected' : '' ?>><?= e($f['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Status</label>
        <select name="status" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-brand transition-colors">
          <option value="">Alle statusser</option>
          <option value="pending"  <?= $filterStatus === 'pending'  ? 'selected' : '' ?>>Afventer</option>
          <option value="accepted" <?= $filterStatus === 'accepted' ? 'selected' : '' ?>>Godkendt</option>
          <option value="denied"   <?= $filterStatus === 'denied'   ? 'selected' : '' ?>>Afvist</option>
        </select>
      </div>
      <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm rounded-lg transition-colors">
        Filtrer
      </button>
      <?php if($filterForm || $filterStatus): ?>
      <a href="<?= url('admin/applications') ?>" class="px-4 py-2 text-slate-400 hover:text-white text-sm transition-colors">
        Nulstil
      </a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Table -->
  <div class="glass rounded-2xl overflow-hidden">
    <?php if(empty($applications)): ?>
    <div class="p-10 text-center text-slate-500">
      Ingen ansøgninger matcher dit filter.
    </div>
    <?php else: ?>
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-slate-800">
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Bruger</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Formular</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Sendt</th>
          <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
          <th class="px-5 py-3.5"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-800/50">
        <?php foreach($applications as $app): ?>
        <?php
          $statusMap = [
            'pending'  => ['Afventer', 'bg-amber-500/15 text-amber-400'],
            'accepted' => ['Godkendt', 'bg-emerald-500/15 text-emerald-400'],
            'denied'   => ['Afvist',   'bg-red-500/15 text-red-400'],
          ];
          [$label, $cls] = $statusMap[$app['status']] ?? ['Ukendt', 'bg-slate-700 text-slate-400'];
        ?>
        <tr class="hover:bg-slate-800/30 transition-colors">
          <td class="px-5 py-3.5">
            <div class="flex items-center gap-2.5">
              <img src="<?= mcHead(e($app['username']), 24) ?>" class="w-6 h-6 rounded" alt="">
              <span class="font-medium text-white"><?= e($app['username']) ?></span>
            </div>
          </td>
          <td class="px-5 py-3.5 text-slate-300"><?= e($app['form_name']) ?></td>
          <td class="px-5 py-3.5 text-slate-400"><?= date('d/m/Y H:i', strtotime($app['created_at'])) ?></td>
          <td class="px-5 py-3.5">
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $cls ?>">
              <?= $label ?>
            </span>
          </td>
          <td class="px-5 py-3.5 text-right">
            <a href="<?= url('admin/applications/' . $app['id']) ?>"
               class="text-xs text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 rounded-lg px-3 py-1.5 transition-colors">
              Gennemse
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

</div>
