<?php
declare(strict_types=1);

namespace Core;

class Application {
    public static Application $instance;
    public Database $db;
    public Session $session;
    public Auth $auth;
    public Router $router;
    public PluginManager $plugins;

    public function __construct() {
        self::$instance = $this;
        $this->db      = new Database();
        $this->session = new Session();
        $this->auth    = new Auth($this->db, $this->session);
        $this->plugins = new PluginManager($this);
        $this->router  = new Router($this);
        $this->registerRoutes();
    }

    private function registerRoutes(): void {
        $r = $this->router;

        // Home
        $r->get('/',        'HomeController@index');

        // Auth
        $r->get('/login',    'AuthController@showLogin');
        $r->post('/login',   'AuthController@login');
        $r->get('/register', 'AuthController@showRegister');
        $r->post('/register','AuthController@register');
        $r->get('/logout',   'AuthController@logout');
        $r->get('/verify/{token}', 'AuthController@verify');

        // Forum
        $r->get('/forum',                        'ForumController@index');
        $r->get('/forum/category/{slug}',        'ForumController@category');
        $r->get('/forum/thread/{id}',            'ForumController@thread');
        $r->get('/forum/new/{categoryId}',       'ForumController@newThread');
        $r->post('/forum/thread/create',         'ForumController@createThread');
        $r->post('/forum/thread/{id}/reply',     'ForumController@replyPost');
        $r->post('/forum/post/{id}/react',       'ForumController@reactPost');

        // Profile
        $r->get('/user/{username}',          'ProfileController@show');
        $r->get('/settings',                 'ProfileController@settings');
        $r->post('/settings/username',       'ProfileController@saveUsername');
        $r->post('/settings/email',          'ProfileController@saveEmail');
        $r->post('/settings/password',       'ProfileController@savePassword');
        $r->post('/settings/delete-account', 'ProfileController@deleteAccount');

        // Admin
        $r->get('/admin',                  'AdminController@dashboard');
        $r->get('/admin/users',            'AdminController@users');
        $r->post('/admin/users/{id}/ban',    'AdminController@banUser');
        $r->post('/admin/users/{id}/role',   'AdminController@changeRole');
        $r->get('/admin/users/{id}/get',     'AdminController@getUser');
        $r->post('/admin/users/{id}/update', 'AdminController@updateUser');
        $r->get('/admin/forum',            'AdminController@forum');
        $r->post('/admin/forum/category',  'AdminController@createCategory');
        $r->post('/admin/forum/category/{id}/delete', 'AdminController@deleteCategory');
        $r->get('/admin/settings',         'AdminController@settings');
        $r->post('/admin/settings',        'AdminController@saveSettings');
        $r->get('/admin/plugins',            'AdminController@plugins');
        $r->post('/admin/plugins/{name}/toggle', 'AdminController@togglePlugin');
        $r->get('/admin/updates',            'AdminController@updates');
        $r->get('/admin/updates/check',      'AdminController@checkUpdate');
        $r->post('/admin/updates/apply',     'AdminController@applyUpdate');

        // Custom pages
        $r->get('/page/{slug}', 'PageController@show');

        // Admin — pages
        $r->get('/admin/pages',                      'AdminController@pages');
        $r->post('/admin/pages/create',              'AdminController@createPage');
        $r->get('/admin/pages/{id}/edit',            'AdminController@editPage');
        $r->post('/admin/pages/{id}/edit',           'AdminController@savePage');
        $r->post('/admin/pages/{id}/delete',         'AdminController@deletePage');

        // API
        $r->get('/api/server',       'ApiController@serverStatus');
        $r->get('/api/online-users', 'ApiController@onlineUsers');
    }

    public function run(): void {
        $this->plugins->loadAll();
        $this->router->dispatch();
    }
}
