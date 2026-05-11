<?php
function isOnline(string $lastSeen): bool {
    return strtotime($lastSeen) > time() - 900;
}
?>

<!-- Edit User Modal -->
<div x-data="editModal()" @open-edit.window="open($event.detail)">
  <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="close()"></div>

    <!-- Modal -->
    <div class="relative glass rounded-2xl w-full max-w-md shadow-2xl" x-transition>
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
        <div class="flex items-center gap-3">
          <img :src="`https://mc-heads.net/avatar/${currentUsername}/32`" class="w-8 h-8 rounded-lg" alt="">
          <div>
            <h3 class="font-semibold text-white">Rediger bruger</h3>
            <p class="text-xs text-slate-500" x-text="'#' + userId"></p>
          </div>
        </div>
        <button @click="close()" class="text-slate-500 hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <!-- Loading -->
      <div x-show="loading" class="flex items-center justify-center py-12">
        <svg class="w-6 h-6 text-brand animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
      </div>

      <!-- Form -->
      <form x-show="!loading" @submit.prevent="save()" class="p-6 space-y-4">
        <!-- Errors -->
        <div x-show="errors.length > 0" class="bg-red-500/10 border border-red-500/20 rounded-xl p-3 space-y-1">
          <template x-for="err in errors" :key="err">
            <p class="text-sm text-red-400 flex items-center gap-1.5">
              <span class="text-red-500">✕</span>
              <span x-text="err"></span>
            </p>
          </template>
        </div>

        <!-- Success -->
        <div x-show="successMsg" x-cloak class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 flex items-center gap-2 text-emerald-400 text-sm">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <span x-text="successMsg"></span>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">Brugernavn</label>
          <div class="relative">
            <input type="text" x-model="form.username" minlength="3" maxlength="30"
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 pr-10 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            <img :src="`https://mc-heads.net/avatar/${form.username}/20`" class="absolute right-3 top-2.5 w-5 h-5 rounded" alt="">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
          <input type="email" x-model="form.email"
                 class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">
            Ny adgangskode
            <span class="text-slate-600 font-normal">(lad stå tom for ingen ændring)</span>
          </label>
          <div class="relative" x-data="{showPw: false}">
            <input :type="showPw ? 'text' : 'password'" x-model="form.password" minlength="8"
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 pr-10 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors"
                   placeholder="Min. 8 tegn">
            <button type="button" @click="showPw = !showPw" class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-300">
              <svg x-show="!showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg x-show="showPw" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1.5">Rolle</label>
          <select x-model="form.role"
                  class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors">
            <option value="member">Medlem</option>
            <option value="moderator">Moderator</option>
            <option value="admin">Administrator</option>
          </select>
        </div>

        <!-- Extra info (read-only) -->
        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
          <div class="bg-slate-900/50 rounded-lg px-3 py-2">
            <p class="text-xs text-slate-500 mb-0.5">Oprettet</p>
            <p class="text-sm text-slate-300" x-text="joinDate"></p>
          </div>
          <div class="bg-slate-900/50 rounded-lg px-3 py-2">
            <p class="text-xs text-slate-500 mb-0.5">Sidst set</p>
            <p class="text-sm text-slate-300" x-text="lastSeen"></p>
          </div>
          <div class="bg-slate-900/50 rounded-lg px-3 py-2">
            <p class="text-xs text-slate-500 mb-0.5">Indlæg</p>
            <p class="text-sm font-semibold text-white" x-text="postCount"></p>
          </div>
          <div class="bg-slate-900/50 rounded-lg px-3 py-2">
            <p class="text-xs text-slate-500 mb-0.5">Status</p>
            <p class="text-sm font-semibold" :class="banned ? 'text-red-400' : 'text-emerald-400'" x-text="banned ? 'Banned' : 'Aktiv'"></p>
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="button" @click="close()"
                  class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-lg transition-colors">
            Annuller
          </button>
          <button type="submit" :disabled="saving"
                  class="flex-1 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors disabled:opacity-60 flex items-center justify-center gap-2">
            <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <span x-text="saving ? 'Gemmer…' : 'Gem ændringer'"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="max-w-7xl">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
      <h1 class="text-2xl font-bold text-white">Brugere
        <span class="text-slate-500 text-lg font-normal">(<?= number_format($total) ?>)</span>
      </h1>
    </div>
    <!-- Search -->
    <form method="GET" action="" class="flex gap-2">
      <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="Søg efter navn eller email…"
             class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-brand w-64 transition-colors">
      <button type="submit" class="px-4 py-2 bg-brand hover:bg-brand-dark text-white text-sm font-medium rounded-lg transition-colors">Søg</button>
      <?php if($search ?? ''): ?>
      <a href="<?= url('admin/users') ?>" class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 text-sm rounded-lg transition-colors">✕</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Stats bar -->
  <?php
    $bannedCount = db()->fetch("SELECT COUNT(*) AS c FROM " . DB_PREFIX . "users WHERE banned = 1")['c'] ?? 0;
    $onlineCount = db()->fetch("SELECT COUNT(*) AS c FROM " . DB_PREFIX . "users WHERE last_seen > DATE_SUB(NOW(), INTERVAL 15 MINUTE)")['c'] ?? 0;
    $adminCount  = db()->fetch("SELECT COUNT(*) AS c FROM " . DB_PREFIX . "users WHERE role = 'admin'")['c'] ?? 0;
  ?>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="glass rounded-xl px-4 py-3 flex items-center gap-3">
      <div class="w-8 h-8 bg-blue-500/15 rounded-lg flex items-center justify-center">
        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
      </div>
      <div><p class="text-lg font-bold text-white"><?= number_format($total) ?></p><p class="text-xs text-slate-500">Total</p></div>
    </div>
    <div class="glass rounded-xl px-4 py-3 flex items-center gap-3">
      <div class="w-8 h-8 bg-emerald-500/15 rounded-lg flex items-center justify-center">
        <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
      </div>
      <div><p class="text-lg font-bold text-white"><?= number_format($onlineCount) ?></p><p class="text-xs text-slate-500">Online nu</p></div>
    </div>
    <div class="glass rounded-xl px-4 py-3 flex items-center gap-3">
      <div class="w-8 h-8 bg-red-500/15 rounded-lg flex items-center justify-center">
        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
      </div>
      <div><p class="text-lg font-bold text-white"><?= number_format($bannedCount) ?></p><p class="text-xs text-slate-500">Banned</p></div>
    </div>
    <div class="glass rounded-xl px-4 py-3 flex items-center gap-3">
      <div class="w-8 h-8 bg-red-500/15 rounded-lg flex items-center justify-center">
        <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
      </div>
      <div><p class="text-lg font-bold text-white"><?= number_format($adminCount) ?></p><p class="text-xs text-slate-500">Admins</p></div>
    </div>
  </div>

  <?php if(empty($users)): ?>
  <div class="glass rounded-2xl p-10 text-center text-slate-500">Ingen brugere fundet.</div>
  <?php else: ?>

  <!-- User cards -->
  <div class="glass rounded-2xl overflow-hidden">
    <div class="divide-y divide-slate-800/60">
      <?php foreach($users as $u): ?>
      <?php $online = isOnline($u['last_seen'] ?? '2000-01-01'); ?>
      <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-800/20 transition-colors" id="user-row-<?= $u['id'] ?>">

        <!-- Avatar + online dot -->
        <div class="relative flex-shrink-0">
          <img src="<?= mcHead(e($u['username']), 40) ?>" class="w-10 h-10 rounded-xl <?= $u['banned'] ? 'opacity-40 grayscale' : '' ?>" alt="">
          <?php if($online && !$u['banned']): ?>
          <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2 border-slate-900"></span>
          <?php elseif($u['banned']): ?>
          <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-red-500 rounded-full border-2 border-slate-900"></span>
          <?php endif; ?>
        </div>

        <!-- Main info -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="font-semibold text-white user-name-text"><?= e($u['username']) ?></span>
            <span class="user-role-badge-<?= $u['id'] ?> text-xs px-2 py-0.5 rounded-full <?= \App\Models\User::roleBadgeClass($u['role']) ?>">
              <?= \App\Models\User::roleLabel($u['role']) ?>
            </span>
            <?php if($u['banned']): ?>
            <span class="text-xs px-2 py-0.5 rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Banned</span>
            <?php endif; ?>
          </div>
          <p class="text-xs text-slate-500 mt-0.5 truncate"><?= e($u['email']) ?></p>
        </div>

        <!-- Stats -->
        <div class="hidden md:flex items-center gap-5 text-center flex-shrink-0">
          <div>
            <p class="text-sm font-bold text-white"><?= number_format((int)$u['post_count']) ?></p>
            <p class="text-xs text-slate-600">Indlæg</p>
          </div>
          <div>
            <p class="text-sm font-bold text-white"><?= number_format((int)$u['thread_count']) ?></p>
            <p class="text-xs text-slate-600">Tråde</p>
          </div>
          <div>
            <p class="text-xs font-medium <?= $online ? 'text-emerald-400' : 'text-slate-400' ?>">
              <?= $online ? 'Online' : timeAgo($u['last_seen'] ?? $u['created_at']) ?>
            </p>
            <p class="text-xs text-slate-600">Sidst set</p>
          </div>
          <div>
            <p class="text-xs text-slate-400"><?= date('d/m/Y', strtotime($u['created_at'])) ?></p>
            <p class="text-xs text-slate-600">Oprettet</p>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2 flex-shrink-0" x-data="{ open: false }">
          <a href="<?= url('user/' . urlencode($u['username'])) ?>" target="_blank"
             class="p-2 text-slate-500 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors"
             title="Se profil">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          </a>

          <button @click="$dispatch('open-edit', <?= htmlspecialchars(json_encode(['id' => $u['id']]), ENT_QUOTES) ?>)"
                  class="p-2 text-slate-500 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors"
                  title="Rediger bruger">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </button>

          <?php if($u['id'] !== auth()->user()['id']): ?>

          <!-- Role dropdown -->
          <div class="relative" x-data="{ roleOpen: false }">
            <button @click="roleOpen = !roleOpen"
                    class="p-2 text-slate-500 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors"
                    title="Skift rolle">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <div x-show="roleOpen" x-cloak @click.outside="roleOpen = false" x-transition
                 class="absolute right-0 mt-2 w-40 bg-slate-900 border border-slate-700 rounded-xl py-1 shadow-xl z-20">
              <p class="text-xs text-slate-500 px-3 py-1.5 uppercase tracking-wider">Sæt rolle</p>
              <?php foreach(['member' => 'Medlem', 'moderator' => 'Moderator', 'admin' => 'Administrator'] as $roleKey => $roleLabel): ?>
              <button onclick="changeRole(<?= $u['id'] ?>, '<?= $roleKey ?>')"
                      @click="roleOpen = false"
                      class="w-full text-left px-3 py-2 text-sm transition-colors <?= $u['role'] === $roleKey ? 'text-brand' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                <?= $roleLabel ?>
                <?php if($u['role'] === $roleKey): ?> ✓<?php endif; ?>
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Ban / Unban -->
          <?php if($u['banned']): ?>
          <button onclick="setBan(<?= $u['id'] ?>, 0)"
                  class="px-3 py-1.5 text-xs font-medium bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25 rounded-lg transition-colors border border-emerald-500/20"
                  title="Ophæv ban">
            Ophæv ban
          </button>
          <?php else: ?>
          <button onclick="setBan(<?= $u['id'] ?>, 1)"
                  class="px-3 py-1.5 text-xs font-medium bg-red-500/15 text-red-400 hover:bg-red-500/25 rounded-lg transition-colors border border-red-500/20"
                  title="Ban bruger">
            Ban
          </button>
          <?php endif; ?>

          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Pagination -->
  <?php if($pages > 1): ?>
  <div class="flex justify-center gap-2 mt-5">
    <?php for($i = 1; $i <= $pages; $i++): ?>
    <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
       class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
              <?= $i === $page ? 'bg-brand text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' ?>">
      <?= $i ?>
    </a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<script>
const csrf = '<?= csrf() ?>';

function editModal() {
  return {
    show:           false,
    loading:        false,
    saving:         false,
    userId:         null,
    currentUsername:'',
    joinDate:       '',
    lastSeen:       '',
    postCount:      0,
    banned:         false,
    errors:         [],
    successMsg:     '',
    form: { username: '', email: '', password: '', role: 'member' },

    async open(detail) {
      this.show       = true;
      this.loading    = true;
      this.errors     = [];
      this.successMsg = '';
      this.userId     = detail.id;
      this.form.password = '';

      const res  = await fetch(`<?= url('admin/users') ?>/${detail.id}/get`);
      const data = await res.json();

      this.loading = false;
      if (!data.ok) { this.errors = [data.error]; return; }

      const u = data.user;
      this.form.username    = u.username;
      this.form.email       = u.email;
      this.form.role        = u.role;
      this.currentUsername  = u.username;
      this.banned           = !!u.banned;
      this.joinDate         = u.created_at ? new Date(u.created_at).toLocaleDateString('da-DK') : '—';
      this.lastSeen         = u.last_seen  ? new Date(u.last_seen).toLocaleDateString('da-DK')  : '—';
      this.postCount        = u.post_count ?? '—';
    },

    close() {
      this.show = false;
      this.successMsg = '';
    },

    async save() {
      this.saving    = true;
      this.errors    = [];
      this.successMsg = '';

      const body = new URLSearchParams({
        username: this.form.username,
        email:    this.form.email,
        password: this.form.password,
        role:     this.form.role,
        _csrf:    csrf,
      });

      const res  = await fetch(`<?= url('admin/users') ?>/${this.userId}/update`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body,
      });
      const data = await res.json();
      this.saving = false;

      if (!data.ok) {
        this.errors = data.errors ?? [data.error ?? 'Ukendt fejl.'];
        return;
      }

      this.currentUsername = data.user.username;
      this.form.password   = '';
      this.successMsg      = 'Ændringer gemt!';

      // Update the row in the table
      const row = document.getElementById(`user-row-${this.userId}`);
      if (row) {
        row.querySelector('img').src = `https://mc-heads.net/avatar/${data.user.username}/40`;
        row.querySelector('.user-name-text').textContent = data.user.username;
      }
    }
  }
}

async function setBan(id, banned) {
  const btn = event.currentTarget;
  btn.disabled = true;
  btn.textContent = '…';

  const res = await fetch(`<?= url('admin/users') ?>/${id}/ban`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `banned=${banned}&_csrf=${csrf}`
  });

  if (res.ok) {
    // Reload to reflect changes cleanly
    location.reload();
  } else {
    btn.disabled = false;
    alert('Fejl — prøv igen.');
  }
}

async function changeRole(id, role) {
  const res = await fetch(`<?= url('admin/users') ?>/${id}/role`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `role=${role}&_csrf=${csrf}`
  });

  if (res.ok) {
    location.reload();
  } else {
    const data = await res.json().catch(() => ({}));
    alert(data.error ?? 'Fejl — prøv igen.');
  }
}
</script>
