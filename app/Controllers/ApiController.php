<?php
declare(strict_types=1);

namespace App\Controllers;

class ApiController extends Controller {
    public function serverStatus(): void {
        $ip   = setting('server_ip', '');
        $port = (int) setting('server_port', '25565');
        if (!$ip) { $this->json(['online' => false]); }
        $this->json(serverStatus($ip, $port));
    }

    public function onlineUsers(): void {
        // Returns recently active users
        $users = db()->fetchAll(
            'SELECT username, last_seen FROM ' . DB_PREFIX . 'users
             WHERE last_seen > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
             ORDER BY last_seen DESC LIMIT 20'
        );
        $this->json(['count' => count($users), 'users' => $users]);
    }
}
