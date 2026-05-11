<?php
$isNew      = $form === null;
$actionUrl  = $isNew
    ? url('admin/applications/forms/create')
    : url('admin/applications/forms/' . $form['id'] . '/update');

$existingQuestions = array_values($questions ?? []);
$jsQuestions = json_encode(array_map(fn($q) => [
    'label'    => $q['label'],
    'type'     => $q['type'],
    'options'  => $q['options'] ?? '',
    'required' => (bool)$q['required'],
], $existingQuestions));
?>

<div class="space-y-6 max-w-2xl"
     x-data="formEditor(<?= htmlspecialchars($jsQuestions, ENT_QUOTES) ?>)">

  <div class="flex items-center gap-3">
    <a href="<?= url('admin/applications/forms') ?>" class="text-slate-400 hover:text-white transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="text-xl font-bold text-white"><?= $isNew ? 'Ny formular' : 'Rediger: ' . e($form['name']) ?></h1>
  </div>

  <?php if($error ?? null): ?>
  <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 flex items-center gap-3 text-red-400 text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
    <?= e($error) ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= $actionUrl ?>">
    <?= csrf_field() ?>

    <!-- Basic info -->
    <div class="glass rounded-2xl p-6 space-y-4 mb-5">
      <h2 class="font-semibold text-white">Grundlæggende oplysninger</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-400 mb-1.5">Navn <span class="text-red-400">*</span></label>
          <input type="text" name="name" required
                 value="<?= e($form['name'] ?? '') ?>"
                 placeholder="fx Staff ansøgning"
                 x-on:input="if(isNew) slug = $event.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors text-sm">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-400 mb-1.5">Slug <span class="text-red-400">*</span></label>
          <input type="text" name="slug" required
                 x-model="slug"
                 placeholder="staff-ansoegning"
                 pattern="[a-z0-9\-]+"
                 title="Kun a-z, 0-9 og bindestreg"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors text-sm font-mono">
          <p class="text-xs text-slate-600 mt-1">/apply/<span x-text="slug || '...'"></span></p>
        </div>
      </div>

      <div>
        <label class="block text-xs font-medium text-slate-400 mb-1.5">Beskrivelse</label>
        <textarea name="description" rows="2" placeholder="Kort beskrivelse af formularen..."
                  class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors text-sm resize-none"><?= e($form['description'] ?? '') ?></textarea>
      </div>

      <div class="flex items-center gap-6">
        <label class="flex items-center gap-2.5 cursor-pointer">
          <input type="checkbox" name="active" value="1"
                 <?= ($form['active'] ?? 1) ? 'checked' : '' ?>
                 class="w-4 h-4 rounded accent-emerald-500">
          <span class="text-sm text-slate-300">Aktiv (synlig for brugere)</span>
        </label>
        <label class="flex items-center gap-2.5 cursor-pointer">
          <input type="checkbox" name="one_per_user" value="1"
                 <?= ($form['one_per_user'] ?? 1) ? 'checked' : '' ?>
                 class="w-4 h-4 rounded accent-emerald-500">
          <span class="text-sm text-slate-300">Kun én ansøgning pr. bruger</span>
        </label>
      </div>
    </div>

    <!-- Questions -->
    <div class="glass rounded-2xl p-6 mb-5">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-white">Spørgsmål</h2>
        <button type="button" @click="addQuestion()"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-brand border border-brand/30 hover:bg-brand/10 rounded-lg transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          Tilføj spørgsmål
        </button>
      </div>

      <div x-show="questions.length === 0" class="py-6 text-center text-slate-500 text-sm">
        Ingen spørgsmål endnu. Klik "Tilføj spørgsmål" for at starte.
      </div>

      <div class="space-y-4">
        <template x-for="(q, i) in questions" :key="i">
          <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-800">
            <div class="flex items-start gap-3">
              <div class="flex-1 space-y-3">

                <!-- Label -->
                <div>
                  <label class="block text-xs text-slate-500 mb-1">Spørgsmål / label</label>
                  <input type="text" :name="'questions['+i+'][label]'" x-model="q.label" required
                         placeholder="fx Hvad er din alder?"
                         class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm placeholder-slate-600 focus:outline-none focus:border-brand transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <!-- Type -->
                  <div>
                    <label class="block text-xs text-slate-500 mb-1">Type</label>
                    <select :name="'questions['+i+'][type]'" x-model="q.type"
                            class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-brand transition-colors">
                      <option value="textarea">Langt svar (textarea)</option>
                      <option value="text">Kort svar (tekst)</option>
                      <option value="select">Dropdown (select)</option>
                      <option value="radio">Valgmuligheder (radio)</option>
                    </select>
                  </div>
                  <!-- Required -->
                  <div class="flex items-end pb-0.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                      <input type="checkbox" :name="'questions['+i+'][required]'" value="1"
                             x-model="q.required"
                             class="w-4 h-4 rounded accent-emerald-500">
                      <span class="text-sm text-slate-300">Obligatorisk</span>
                    </label>
                  </div>
                </div>

                <!-- Options (for select/radio) -->
                <div x-show="q.type === 'select' || q.type === 'radio'">
                  <label class="block text-xs text-slate-500 mb-1">Valgmuligheder (kommasepareret)</label>
                  <input type="text" :name="'questions['+i+'][options]'" x-model="q.options"
                         placeholder="Ja, Nej, Måske"
                         class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm placeholder-slate-600 focus:outline-none focus:border-brand transition-colors">
                </div>
                <!-- Hidden options field when not needed -->
                <input type="hidden" :name="'questions['+i+'][options]'" :value="q.options"
                       x-show="q.type !== 'select' && q.type !== 'radio'">

              </div>

              <!-- Remove -->
              <button type="button" @click="questions.splice(i, 1)"
                      class="text-slate-600 hover:text-red-400 transition-colors mt-1 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>

    <div class="flex items-center justify-between">
      <a href="<?= url('admin/applications/forms') ?>" class="text-sm text-slate-400 hover:text-white transition-colors">
        Annuller
      </a>
      <button type="submit" class="px-6 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">
        <?= $isNew ? 'Opret formular' : 'Gem ændringer' ?>
      </button>
    </div>

  </form>
</div>

<script>
function formEditor(initial) {
  return {
    isNew:     <?= $isNew ? 'true' : 'false' ?>,
    slug:      <?= json_encode($form['slug'] ?? '') ?>,
    questions: initial.length ? initial : [],
    addQuestion() {
      this.questions.push({ label: '', type: 'textarea', options: '', required: true });
    },
  };
}
</script>
