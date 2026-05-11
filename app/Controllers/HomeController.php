<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\User;

class HomeController extends Controller {
    public function index(): void {
        $recentThreads = ForumThread::recent(5);
        $recentPosts   = ForumPost::recent(5);
        $totalUsers    = User::count();
        $totalPosts    = ForumPost::count();
        $totalThreads  = ForumThread::count();

        $serverIp   = setting('server_ip', '');
        $serverPort = (int) setting('server_port', '25565');
        $serverStatus = null;
        if ($serverIp) {
            $cacheKey  = 'server_status_cache';
            $cacheTime = 60;
            $cached    = session()->get($cacheKey);
            if ($cached && isset($cached['ts']) && (time() - $cached['ts']) < $cacheTime) {
                $serverStatus = $cached['data'];
            } else {
                $serverStatus = serverStatus($serverIp, $serverPort);
                session()->set($cacheKey, ['ts' => time(), 'data' => $serverStatus]);
            }
        }

        $this->render('home', compact(
            'recentThreads', 'recentPosts', 'totalUsers', 'totalPosts',
            'totalThreads', 'serverStatus', 'serverIp'
        ));
    }
}
