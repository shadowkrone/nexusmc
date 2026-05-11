<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">

  <div class="mb-6">
    <a href="<?= url('applications') ?>" class="text-sm text-slate-500 hover:text-white transition-colors inline-flex items-center gap-1.5">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Alle formularer
    </a>
    <h1 class="text-2xl font-bold text-white mt-3"><?= e($form['name']) ?></h1>
    <?php if($form['description']): ?>
    <p class="text-slate-400 mt-1"><?= e($form['description']) ?></p>
    <?php endif; ?>
  </div>

  <?php if($error ?? null): ?>
  <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-5 flex items-center gap-3 text-red-400 text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
    <?= e($error) ?>
  </div>
  <?php endif; ?>

  <?php if(empty($questions)): ?>
  <div class="glass rounded-2xl p-8 text-center text-slate-500">
    Denne formular har ingen spørgsmål endnu.
  </div>
  <?php else: ?>
  <form method="POST" action="<?= url('apply/' . e($form['slug'])) ?>" class="space-y-5">
    <?= csrf_field() ?>

    <?php foreach($questions as $q): ?>
    <div class="glass rounded-2xl p-5">
      <label class="block text-sm font-medium text-slate-200 mb-2">
        <?= e($q['label']) ?>
        <?php if($q['required']): ?>
        <span class="text-red-400 ml-0.5">*</span>
        <?php endif; ?>
      </label>

      <?php if($q['type'] === 'textarea'): ?>
        <textarea name="q_<?= $q['id'] ?>" rows="5"
                  <?= $q['required'] ? 'required' : '' ?>
                  class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors resize-none"></textarea>

      <?php elseif($q['type'] === 'text'): ?>
        <input type="text" name="q_<?= $q['id'] ?>"
               <?= $q['required'] ? 'required' : '' ?>
               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">

      <?php elseif($q['type'] === 'select'): ?>
        <select name="q_<?= $q['id'] ?>" <?= $q['required'] ? 'required' : '' ?>
                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
          <option value="">— Vælg —</option>
          <?php foreach(array_filter(array_map('trim', explode(',', $q['options'] ?? ''))) as $opt): ?>
          <option value="<?= e($opt) ?>"><?= e($opt) ?></option>
          <?php endforeach; ?>
        </select>

      <?php elseif($q['type'] === 'radio'): ?>
        <div class="space-y-2 mt-1">
          <?php foreach(array_filter(array_map('trim', explode(',', $q['options'] ?? ''))) as $opt): ?>
          <label class="flex items-center gap-3 cursor-pointer group">
            <input type="radio" name="q_<?= $q['id'] ?>" value="<?= e($opt) ?>"
                   <?= $q['required'] ? 'required' : '' ?>
                   class="w-4 h-4 accent-emerald-500">
            <span class="text-sm text-slate-300 group-hover:text-white transition-colors"><?= e($opt) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <div class="flex items-center justify-between">
      <p class="text-xs text-slate-600"><span class="text-red-400">*</span> Obligatoriske felter</p>
      <button type="submit" class="px-6 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">
        Send ansøgning
      </button>
    </div>
  </form>
  <?php endif; ?>

</div>
