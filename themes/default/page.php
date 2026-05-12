<?php $pageTitle = e($page['title']); ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
  <h1 class="text-3xl font-bold text-white mb-8"><?= e($page['title']) ?></h1>
  <div class="glass rounded-2xl p-8 prose-dark">
    <?= $page['content'] ?>
  </div>
</div>
