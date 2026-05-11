<?php
declare(strict_types=1);

namespace Plugin\Applications\Controllers;

class AdminApplicationController extends PluginController {

    public function index(): void {
        $this->requireAdmin();

        $filterForm   = (int)($_GET['form']   ?? 0);
        $filterStatus = $_GET['status'] ?? '';
        if (!in_array($filterStatus, ['pending', 'accepted', 'denied'])) $filterStatus = '';

        $where  = '1=1';
        $params = [];
        if ($filterForm)   { $where .= ' AND a.form_id = ?'; $params[] = $filterForm; }
        if ($filterStatus) { $where .= ' AND a.status = ?';  $params[] = $filterStatus; }

        $applications = db()->fetchAll(
            'SELECT a.*, f.name AS form_name, u.username
             FROM ' . DB_PREFIX . 'applications a
             JOIN ' . DB_PREFIX . 'app_forms f ON a.form_id = f.id
             JOIN ' . DB_PREFIX . 'users u ON a.user_id = u.id
             WHERE ' . $where . '
             ORDER BY a.created_at DESC',
            $params
        );

        $forms = db()->fetchAll('SELECT id, name FROM ' . DB_PREFIX . 'app_forms ORDER BY name');

        $counts = [
            'total'    => db()->count(DB_PREFIX . 'applications', '1'),
            'pending'  => db()->count(DB_PREFIX . 'applications', 'status = ?', ['pending']),
            'accepted' => db()->count(DB_PREFIX . 'applications', 'status = ?', ['accepted']),
            'denied'   => db()->count(DB_PREFIX . 'applications', 'status = ?', ['denied']),
        ];

        $this->renderAdmin('index', compact('applications', 'forms', 'filterForm', 'filterStatus', 'counts'));
    }

    public function review(int $id): void {
        $this->requireAdmin();

        $application = db()->fetch(
            'SELECT a.*, f.name AS form_name, u.username, u.id AS user_id
             FROM ' . DB_PREFIX . 'applications a
             JOIN ' . DB_PREFIX . 'app_forms f ON a.form_id = f.id
             JOIN ' . DB_PREFIX . 'users u ON a.user_id = u.id
             WHERE a.id = ?',
            [$id]
        );
        if (!$application) { http_response_code(404); return; }

        $qa = db()->fetchAll(
            'SELECT q.label, q.type, COALESCE(ans.answer, \'\') AS answer
             FROM ' . DB_PREFIX . 'app_questions q
             LEFT JOIN ' . DB_PREFIX . 'app_answers ans
               ON ans.question_id = q.id AND ans.application_id = ?
             WHERE q.form_id = ?
             ORDER BY q.sort_order, q.id',
            [$id, $application['form_id']]
        );

        $notes = db()->fetchAll(
            'SELECT n.*, u.username AS admin_username
             FROM ' . DB_PREFIX . 'app_notes n
             JOIN ' . DB_PREFIX . 'users u ON n.admin_id = u.id
             WHERE n.application_id = ?
             ORDER BY n.created_at ASC',
            [$id]
        );

        $this->renderAdmin('review', compact('application', 'qa', 'notes'));
    }

    public function setStatus(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect("admin/applications/{$id}"); }

        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['pending', 'accepted', 'denied'])) {
            flash('error', 'Ugyldig status.');
            redirect("admin/applications/{$id}");
        }

        db()->update(
            DB_PREFIX . 'applications',
            ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')],
            'id = ?', [$id]
        );
        flash('success', 'Status opdateret.');
        redirect("admin/applications/{$id}");
    }

    public function addNote(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect("admin/applications/{$id}"); }

        $body = trim($_POST['body'] ?? '');
        if (!$body) { flash('error', 'Note kan ikke være tom.'); redirect("admin/applications/{$id}"); }

        db()->insert(DB_PREFIX . 'app_notes', [
            'application_id' => $id,
            'admin_id'       => auth()->user()['id'],
            'body'           => $body,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
        flash('success', 'Note tilføjet.');
        redirect("admin/applications/{$id}");
    }

    public function delete(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect('admin/applications'); }

        db()->delete(DB_PREFIX . 'app_answers',  'application_id = ?', [$id]);
        db()->delete(DB_PREFIX . 'app_notes',    'application_id = ?', [$id]);
        db()->delete(DB_PREFIX . 'applications', 'id = ?',             [$id]);

        flash('success', 'Ansøgning slettet.');
        redirect('admin/applications');
    }

    public function forms(): void {
        $this->requireAdmin();

        $forms = db()->fetchAll(
            'SELECT f.*, COUNT(a.id) AS application_count,
                    SUM(a.status = \'pending\')  AS pending_count,
                    SUM(a.status = \'accepted\') AS accepted_count
             FROM ' . DB_PREFIX . 'app_forms f
             LEFT JOIN ' . DB_PREFIX . 'applications a ON a.form_id = f.id
             GROUP BY f.id
             ORDER BY f.name'
        );

        $this->renderAdmin('forms', compact('forms'));
    }

    public function newForm(): void {
        $this->requireAdmin();
        $this->renderAdmin('form-edit', ['form' => null, 'questions' => [], 'error' => flash('error')]);
    }

    public function createForm(): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect('admin/applications/forms/new'); }

        [$name, $slug, $description, $active, $onePerUser] = $this->formFields();

        if (!$name || !$slug) { flash('error', 'Navn og slug er påkrævet.'); redirect('admin/applications/forms/new'); }
        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) { flash('error', 'Slug må kun indeholde a-z, 0-9 og -.'); redirect('admin/applications/forms/new'); }
        if (db()->fetch('SELECT id FROM ' . DB_PREFIX . 'app_forms WHERE slug = ?', [$slug])) {
            flash('error', 'Slug er allerede i brug.'); redirect('admin/applications/forms/new');
        }

        $formId = db()->insert(DB_PREFIX . 'app_forms', [
            'name'         => $name,
            'slug'         => $slug,
            'description'  => $description,
            'active'       => $active,
            'one_per_user' => $onePerUser,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        $this->saveQuestions($formId, $_POST['questions'] ?? []);

        flash('success', 'Formular oprettet.');
        redirect("admin/applications/forms/{$formId}/edit");
    }

    public function editForm(int $id): void {
        $this->requireAdmin();

        $form = db()->fetch('SELECT * FROM ' . DB_PREFIX . 'app_forms WHERE id = ?', [$id]);
        if (!$form) { http_response_code(404); return; }

        $questions = db()->fetchAll(
            'SELECT * FROM ' . DB_PREFIX . 'app_questions WHERE form_id = ? ORDER BY sort_order, id',
            [$id]
        );

        $this->renderAdmin('form-edit', ['form' => $form, 'questions' => $questions, 'error' => flash('error')]);
    }

    public function updateForm(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect("admin/applications/forms/{$id}/edit"); }

        $form = db()->fetch('SELECT * FROM ' . DB_PREFIX . 'app_forms WHERE id = ?', [$id]);
        if (!$form) { http_response_code(404); return; }

        [$name, $slug, $description, $active, $onePerUser] = $this->formFields();

        if (!$name || !$slug) { flash('error', 'Navn og slug er påkrævet.'); redirect("admin/applications/forms/{$id}/edit"); }
        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) { flash('error', 'Slug må kun indeholde a-z, 0-9 og -.'); redirect("admin/applications/forms/{$id}/edit"); }
        if (db()->fetch('SELECT id FROM ' . DB_PREFIX . 'app_forms WHERE slug = ? AND id != ?', [$slug, $id])) {
            flash('error', 'Slug er allerede i brug.'); redirect("admin/applications/forms/{$id}/edit");
        }

        db()->update(DB_PREFIX . 'app_forms', [
            'name'         => $name,
            'slug'         => $slug,
            'description'  => $description,
            'active'       => $active,
            'one_per_user' => $onePerUser,
        ], 'id = ?', [$id]);

        db()->delete(DB_PREFIX . 'app_questions', 'form_id = ?', [$id]);
        $this->saveQuestions($id, $_POST['questions'] ?? []);

        flash('success', 'Formular opdateret.');
        redirect("admin/applications/forms/{$id}/edit");
    }

    public function deleteForm(int $id): void {
        $this->requireAdmin();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect('admin/applications/forms'); }

        $apps = db()->fetchAll('SELECT id FROM ' . DB_PREFIX . 'applications WHERE form_id = ?', [$id]);
        foreach ($apps as $app) {
            db()->delete(DB_PREFIX . 'app_answers', 'application_id = ?', [$app['id']]);
            db()->delete(DB_PREFIX . 'app_notes',   'application_id = ?', [$app['id']]);
        }
        db()->delete(DB_PREFIX . 'applications',  'form_id = ?', [$id]);
        db()->delete(DB_PREFIX . 'app_questions', 'form_id = ?', [$id]);
        db()->delete(DB_PREFIX . 'app_forms',     'id = ?',      [$id]);

        flash('success', 'Formular og alle tilhørende ansøgninger slettet.');
        redirect('admin/applications/forms');
    }

    private function formFields(): array {
        return [
            trim($_POST['name']         ?? ''),
            trim($_POST['slug']         ?? ''),
            trim($_POST['description']  ?? ''),
            isset($_POST['active'])       ? 1 : 0,
            isset($_POST['one_per_user']) ? 1 : 0,
        ];
    }

    private function saveQuestions(int $formId, array $questions): void {
        $order = 0;
        foreach ($questions as $q) {
            $label = trim($q['label'] ?? '');
            if ($label === '') continue;
            $type    = in_array($q['type'] ?? '', ['text', 'textarea', 'select', 'radio']) ? $q['type'] : 'textarea';
            $options = trim($q['options'] ?? '');
            db()->insert(DB_PREFIX . 'app_questions', [
                'form_id'    => $formId,
                'label'      => $label,
                'type'       => $type,
                'options'    => $options !== '' ? $options : null,
                'required'   => isset($q['required']) ? 1 : 0,
                'sort_order' => $order++,
            ]);
        }
    }
}
