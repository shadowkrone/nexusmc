<?php $pageTitle = e($thread['title']); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">

  <!-- Breadcrumb -->
  <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6 flex-wrap">
    <a href="<?= url('forum') ?>" class="hover:text-brand transition-colors">Forum</a>
    <span>›</span>
    <a href="<?= url('forum/category/' . urlencode($category['slug'])) ?>" class="hover:text-brand transition-colors"><?= e($category['name']) ?></a>
    <span>›</span>
    <span class="text-slate-300 truncate"><?= e(mb_strimwidth($thread['title'], 0, 50, '…')) ?></span>
  </nav>

  <!-- Thread header -->
  <div class="mb-8">
    <div class="flex items-start gap-3 flex-wrap">
      <?php if($thread['pinned']): ?><span class="text-sm bg-amber-500/20 text-amber-400 border border-amber-500/25 px-2 py-0.5 rounded-lg">📌 Fast</span><?php endif; ?>
      <?php if($thread['locked']): ?><span class="text-sm bg-slate-700 text-slate-400 px-2 py-0.5 rounded-lg">🔒 Låst</span><?php endif; ?>
    </div>
    <h1 class="text-2xl sm:text-3xl font-bold text-white mt-2"><?= e($thread['title']) ?></h1>
    <div class="flex items-center gap-4 mt-3 text-sm text-slate-500">
      <span><?= $total + 1 ?> indlæg</span>
      <span><?= number_format($thread['views']) ?> visninger</span>
    </div>
  </div>

  <!-- Posts -->
  <div class="space-y-4">
    <!-- OP post -->
    <div class="glass rounded-2xl overflow-hidden">
      <div class="flex gap-0">
        <!-- Sidebar (author info) -->
        <div class="hidden sm:flex flex-col items-center p-5 bg-slate-900/50 border-r border-slate-800 w-36 flex-shrink-0">
          <img src="<?= mcHead(e($thread['username']), 64) ?>" class="w-16 h-16 rounded-xl mb-2" alt="">
          <a href="<?= url('user/' . urlencode($thread['username'])) ?>" class="font-semibold text-white text-sm text-center hover:text-brand transition-colors"><?= e($thread['username']) ?></a>
          <span class="mt-1 text-xs px-2 py-0.5 rounded-full <?= \App\Models\User::roleBadgeClass($thread['role']) ?>">
            <?= \App\Models\User::roleLabel($thread['role']) ?>
          </span>
          <p class="text-xs text-slate-600 mt-2 text-center">Medlem siden<br><?= date('M Y', strtotime($thread['user_joined'])) ?></p>
        </div>
        <!-- Content -->
        <div class="flex-1 p-5 min-w-0">
          <div class="flex items-center justify-between mb-4 sm:hidden">
            <div class="flex items-center gap-2">
              <img src="<?= mcHead(e($thread['username']), 28) ?>" class="w-7 h-7 rounded" alt="">
              <span class="font-semibold text-white text-sm"><?= e($thread['username']) ?></span>
            </div>
            <span class="text-xs text-slate-500"><?= timeAgo($thread['created_at']) ?></span>
          </div>
          <div class="prose-dark"><?= nl2br(e($thread['body'])) ?></div>
          <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-800">
            <span class="text-xs text-slate-600"><?= date('d/m/Y H:i', strtotime($thread['created_at'])) ?></span>
            <div class="flex items-center gap-2">
              <?php if(auth()->check()): ?>
              <button onclick="document.getElementById('reply-form').scrollIntoView({behavior:'smooth'})"
                      class="text-xs text-slate-500 hover:text-brand transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                Svar
              </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reply posts -->
    <?php foreach($posts as $post): ?>
    <div id="post-<?= $post['id'] ?>" class="glass rounded-2xl overflow-hidden">
      <div class="flex">
        <div class="hidden sm:flex flex-col items-center p-5 bg-slate-900/50 border-r border-slate-800 w-36 flex-shrink-0">
          <img src="<?= mcHead(e($post['username']), 64) ?>" class="w-16 h-16 rounded-xl mb-2" alt="">
          <a href="<?= url('user/' . urlencode($post['username'])) ?>" class="font-semibold text-white text-sm text-center hover:text-brand transition-colors"><?= e($post['username']) ?></a>
          <span class="mt-1 text-xs px-2 py-0.5 rounded-full <?= \App\Models\User::roleBadgeClass($post['role']) ?>">
            <?= \App\Models\User::roleLabel($post['role']) ?>
          </span>
          <p class="text-xs text-slate-600 mt-2 text-center"><?= number_format($post['user_post_count']) ?> indlæg</p>
        </div>
        <div class="flex-1 p-5 min-w-0">
          <div class="flex items-center justify-between mb-4 sm:hidden">
            <div class="flex items-center gap-2">
              <img src="<?= mcHead(e($post['username']), 28) ?>" class="w-7 h-7 rounded" alt="">
              <span class="font-semibold text-white text-sm"><?= e($post['username']) ?></span>
            </div>
            <span class="text-xs text-slate-500"><?= timeAgo($post['created_at']) ?></span>
          </div>
          <div class="prose-dark"><?= nl2br(e($post['body'])) ?></div>
          <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-800">
            <span class="text-xs text-slate-600"><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></span>
            <?php if(auth()->check()): ?>
            <button onclick="document.getElementById('reply-form').scrollIntoView({behavior:'smooth'})"
                    class="text-xs text-slate-500 hover:text-brand transition-colors flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
              Svar
            </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if($pages > 1): ?>
  <div class="flex justify-center gap-2 mt-6">
    <?php for($i=1;$i<=$pages;$i++): ?>
    <a href="?page=<?= $i ?>" class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors <?= $i===$page ? 'bg-brand text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' ?>">
      <?= $i ?>
    </a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>

  <!-- Reply form -->
  <?php if(auth()->check() && !$thread['locked']): ?>
  <div id="reply-form" class="mt-8 glass rounded-2xl p-6">
    <h3 class="font-semibold text-white mb-4">Skriv et svar</h3>
    <form method="POST" action="<?= url('forum/thread/' . $thread['id'] . '/reply') ?>">
      <?= csrf_field() ?>
      <textarea name="body" rows="5" required
                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand/30 transition-colors resize-none"
                placeholder="Skriv dit svar her..."></textarea>
      <div class="flex justify-end mt-3">
        <button type="submit" class="px-6 py-2.5 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">
          Send svar
        </button>
      </div>
    </form>
  </div>
  <?php elseif(!auth()->check()): ?>
  <div id="bottom" class="mt-8 glass rounded-2xl p-6 text-center">
    <p class="text-slate-400 mb-3">Log ind for at svare på denne tråd.</p>
    <a href="<?= url('login') ?>" class="inline-block px-5 py-2 bg-brand hover:bg-brand-dark text-white font-semibold rounded-lg transition-colors">Log ind</a>
  </div>
  <?php elseif($thread['locked']): ?>
  <div class="mt-8 glass rounded-2xl p-5 text-center">
    <p class="text-slate-500 text-sm">🔒 Denne tråd er låst og kan ikke besvares.</p>
  </div>
  <?php endif; ?>
</div>
