<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Page;

class PageController extends Controller {
    public function show(string $slug): void {
        $page = Page::findBySlug($slug);
        if (!$page) { http_response_code(404); return; }
        $this->render('page', compact('page'));
    }
}
