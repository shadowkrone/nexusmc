<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumPost;

class ForumController extends Controller {
    public function index(): void {
        $categories = ForumCategory::withStats();
        $this->render('forum/index', compact('categories'));
    }

    public function category(string $slug): void {
        $category = ForumCategory::findBySlug($slug);
        if (!$category) { http_response_code(404); $this->render('404', [], false); return; }

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $threads = ForumThread::forCategory($category['id'], $page);
        $total   = ForumThread::countForCategory($category['id']);
        $pages   = (int)ceil($total / 20);

        $this->render('forum/category', compact('category', 'threads', 'page', 'pages', 'total'));
    }

    public function thread(int $id): void {
        $thread = ForumThread::withAuthor($id);
        if (!$thread) { http_response_code(404); $this->render('404', [], false); return; }

        ForumThread::incrementViews($id);

        $page  = max(1, (int)($_GET['page'] ?? 1));
        $posts = ForumPost::forThread($id, $page);
        $total = ForumPost::countForThread($id);
        $pages = (int)ceil($total / 15);

        $category = ForumCategory::find($thread['category_id']);
        $this->render('forum/thread', compact('thread', 'posts', 'page', 'pages', 'category'));
    }

    public function newThread(int $categoryId): void {
        $this->requireAuth();
        $category = ForumCategory::find($categoryId);
        if (!$category) { http_response_code(404); $this->render('404', [], false); return; }
        $this->render('forum/new-thread', ['category' => $category, 'error' => flash('error')]);
    }

    public function createThread(): void {
        $this->requireAuth();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); $this->back(); }

        $categoryId = (int)($_POST['category_id'] ?? 0);
        $title      = trim($_POST['title'] ?? '');
        $body       = trim($_POST['body'] ?? '');

        if (!$title || !$body || !$categoryId) {
            flash('error', 'Udfyld alle felter.');
            redirect("forum/new/{$categoryId}");
        }
        if (strlen($title) < 3 || strlen($title) > 100) {
            flash('error', 'Titel skal være 3–100 tegn.');
            redirect("forum/new/{$categoryId}");
        }

        $now = date('Y-m-d H:i:s');
        $threadId = ForumThread::create([
            'category_id' => $categoryId,
            'user_id'     => auth()->user()['id'],
            'title'       => $title,
            'body'        => $body,
            'views'       => 0,
            'pinned'      => 0,
            'locked'      => 0,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        redirect("forum/thread/{$threadId}");
    }

    public function replyPost(int $id): void {
        $this->requireAuth();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); $this->back(); }

        $body = trim($_POST['body'] ?? '');
        if (!$body) { flash('error', 'Svaret kan ikke være tomt.'); redirect("forum/thread/{$id}"); }

        $thread = ForumThread::find($id);
        if (!$thread || $thread['locked']) { flash('error', 'Tråden er låst.'); redirect("forum/thread/{$id}"); }

        ForumPost::create([
            'thread_id'  => $id,
            'user_id'    => auth()->user()['id'],
            'body'       => $body,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        ForumThread::update(['updated_at' => date('Y-m-d H:i:s')], $id);
        $count = ForumPost::countForThread($id);
        $page  = (int)ceil($count / 15);
        redirect("forum/thread/{$id}?page={$page}#bottom");
    }

    public function reactPost(int $id): void {
        // Reactions placeholder
        $this->json(['ok' => true]);
    }
}
