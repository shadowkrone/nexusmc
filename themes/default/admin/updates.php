<div class="max-w-3xl" x-data="updater()">

  <div class="flex items-center justify-between mb-7">
    <div>
      <h1 class="text-2xl font-bold text-white">Opdateringer</h1>
      <p class="text-sm text-slate-400 mt-1">Nuværende version: <span class="text-white font-mono font-semibold">v<?= APP_VERSION ?></span></p>
    </div>
    <button @click="check()" :disabled="checking"
            class="flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium text-sm rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
      <svg class="w-4 h-4" :class="{'animate-spin': checking}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
      <span x-text="checking ? 'Tjekker…' : 'Tjek for opdateringer'"></span>
    </button>
  </div>

  <?php if(!$repo): ?>
  <!-- No repo configured -->
  <div class="glass rounded-2xl p-8 flex items-start gap-4">
    <div class="w-10 h-10 bg-amber-500/15 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
      <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div>
      <p class="font-semibold text-white mb-1">GitHub repo ikke konfigureret</p>
      <p class="text-sm text-slate-400 mb-3">Sæt dit GitHub repo-navn under <a href="<?= url('admin/settings') ?>" class="text-brand hover:underline">Indstillinger → Auto-opdatering</a> for at aktivere automatiske opdateringer.</p>
      <p class="text-xs text-slate-500">Format: <code class="bg-slate-800 px-1.5 py-0.5 rounded">ditbrugernavn/nexusmc</code></p>
    </div>
  </div>

  <?php elseif(!$canWrite): ?>
  <!-- File permission error -->
  <div class="glass rounded-2xl p-8 flex items-start gap-4 border border-red-500/20">
    <div class="w-10 h-10 bg-red-500/15 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
      <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    </div>
    <div>
      <p class="font-semibold text-white mb-1">Manglende skrivetilladelse</p>
      <p class="text-sm text-slate-400">PHP-processen har ikke tilladelse til at skrive til filerne. Kør dette på serveren:</p>
      <code class="block mt-2 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-brand font-mono">chmod -R 755 /var/www/nolifegroup.com/public_html/</code>
    </div>
  </div>

  <?php else: ?>

  <!-- Current version card -->
  <div class="glass rounded-2xl p-5 mb-4 flex items-center gap-4">
    <div class="w-10 h-10 bg-brand/15 border border-brand/25 rounded-xl flex items-center justify-center flex-shrink-0">
      <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
    </div>
    <div class="flex-1">
      <p class="font-semibold text-white">Installeret version</p>
      <p class="text-sm text-slate-400">NexusMC v<?= APP_VERSION ?></p>
    </div>
    <span class="text-xs bg-brand/15 text-brand border border-brand/25 px-3 py-1 rounded-full font-medium">Aktuel</span>
  </div>

  <!-- Error state -->
  <div x-show="error" x-cloak class="glass rounded-2xl p-5 mb-4 flex items-center gap-3 border border-red-500/20">
    <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
    <p class="text-sm text-red-400" x-text="error"></p>
  </div>

  <!-- Up to date -->
  <div x-show="checked && !hasUpdate && !error" x-cloak class="glass rounded-2xl p-6 text-center">
    <div class="w-12 h-12 bg-emerald-500/15 rounded-full flex items-center justify-center mx-auto mb-3">
      <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    </div>
    <p class="font-semibold text-white mb-1">Du har den nyeste version</p>
    <p class="text-sm text-slate-400">NexusMC v<?= APP_VERSION ?> er den seneste release.</p>
  </div>

  <!-- Update available -->
  <div x-show="hasUpdate" x-cloak class="glass rounded-2xl overflow-hidden border border-brand/20">
    <div class="bg-brand/10 border-b border-brand/20 px-5 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 gradient-brand rounded-lg flex items-center justify-center">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </div>
        <div>
          <p class="font-semibold text-white">Opdatering tilgængelig!</p>
          <p class="text-xs text-slate-400">v<?= APP_VERSION ?> → <span class="text-brand font-semibold" x-text="'v' + latestVersion"></span></p>
        </div>
      </div>
      <a :href="releaseUrl" target="_blank" class="text-xs text-slate-400 hover:text-brand transition-colors">Se på GitHub →</a>
    </div>

    <!-- Changelog -->
    <div class="p-5">
      <h3 class="text-sm font-semibold text-slate-300 mb-3">Hvad er nyt — <span x-text="releaseName"></span></h3>
      <div class="bg-slate-900/60 rounded-xl p-4 max-h-64 overflow-y-auto">
        <pre class="text-xs text-slate-400 whitespace-pre-wrap font-mono leading-relaxed" x-text="changelog || 'Ingen changelog tilgængelig.'"></pre>
      </div>
      <p class="text-xs text-slate-600 mt-2">Udgivet <span x-text="published"></span></p>
    </div>

    <!-- Update button -->
    <div class="px-5 pb-5">
      <div x-show="!updating && !updateDone" class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-3 mb-4 text-xs text-amber-400">
        ⚠️ <strong>Backup:</strong> En kopi af <code class="bg-slate-800 px-1 rounded">config/config.php</code> gemmes automatisk i <code class="bg-slate-800 px-1 rounded">/storage/backups/</code> inden opdatering. Din <code class="bg-slate-800 px-1 rounded">/plugins/</code> mappe røres ikke.
      </div>

      <!-- Progress -->
      <div x-show="updating" x-cloak class="flex items-center gap-3 mb-4 bg-slate-900/50 rounded-xl p-4">
        <svg class="w-5 h-5 text-brand animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
        <div>
          <p class="text-sm font-medium text-white">Opdaterer…</p>
          <p class="text-xs text-slate-500" x-text="updateStatus"></p>
        </div>
      </div>

      <!-- Success -->
      <div x-show="updateDone" x-cloak class="flex items-center gap-3 mb-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4">
        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <p class="text-sm text-emerald-400 font-medium" x-text="updateMessage"></p>
      </div>

      <button x-show="!updating && !updateDone"
              @click="applyUpdate()"
              class="w-full py-3 bg-brand hover:bg-brand-dark text-white font-semibold rounded-xl transition-colors shadow-lg shadow-brand/20 flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Opdater til v<span x-text="latestVersion"></span> nu
      </button>

      <button x-show="updateDone" x-cloak
              onclick="location.reload()"
              class="w-full py-3 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-xl transition-colors">
        Genindlæs side
      </button>
    </div>
  </div>

  <?php endif; ?>
</div>

<script>
function updater() {
  return {
    checking:      false,
    checked:       false,
    hasUpdate:     false,
    updating:      false,
    updateDone:    false,
    error:         null,
    latestVersion: '',
    releaseName:   '',
    changelog:     '',
    releaseUrl:    '',
    published:     '',
    zipUrl:        '',
    updateStatus:  'Downloader filer…',
    updateMessage: '',

    async check() {
      this.checking = true;
      this.error    = null;
      this.checked  = false;
      this.hasUpdate = false;

      try {
        const res  = await fetch('<?= url('admin/updates/check') ?>');
        const data = await res.json();

        if (!data.ok) {
          this.error = data.message;
        } else {
          this.checked       = true;
          this.hasUpdate     = data.has_update;
          this.latestVersion = data.latest;
          this.releaseName   = data.name;
          this.changelog     = data.body;
          this.releaseUrl    = data.html_url;
          this.zipUrl        = data.zip_url;
          this.published     = data.published
            ? new Date(data.published).toLocaleDateString('da-DK', {day:'numeric',month:'long',year:'numeric'})
            : '';
        }
      } catch (e) {
        this.error = 'Netværksfejl — tjek din internetforbindelse.';
      }

      this.checking = false;
    },

    async applyUpdate() {
      if (!confirm(`Vil du opdatere NexusMC til v${this.latestVersion}?\n\nDin config og plugins bevares. En backup oprettes automatisk.`)) return;

      this.updating     = true;
      this.updateStatus = 'Downloader og udpakker filer fra GitHub…';

      try {
        const res  = await fetch('<?= url('admin/updates/apply') ?>', {
          method:  'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body:    `zip_url=${encodeURIComponent(this.zipUrl)}&_csrf=<?= csrf() ?>`
        });
        const data = await res.json();

        this.updating     = false;
        this.updateDone   = data.ok;
        this.updateMessage = data.message;

        if (!data.ok) {
          this.error = data.message;
        }
      } catch (e) {
        this.updating = false;
        this.error    = 'Netværksfejl under opdatering.';
      }
    }
  }
}
</script>
