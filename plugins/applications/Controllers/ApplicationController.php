<?php
declare(strict_types=1);

namespace Plugin\Applications\Controllers;

class ApplicationController extends PluginController {

    public function list(): void {
        $forms = db()->fetchAll(
            'SELECT * FROM ' . DB_PREFIX . 'app_forms WHERE active = 1 ORDER BY name'
        );

        $applied = [];
        if (auth()->check()) {
            $userId = auth()->user()['id'];
            foreach ($forms as $form) {
                $row = db()->fetch(
                    'SELECT id, status FROM ' . DB_PREFIX . 'applications WHERE form_id = ? AND user_id = ?',
                    [$form['id'], $userId]
                );
                if ($row) $applied[$form['id']] = $row;
            }
        }

        $this->render('list', ['pageTitle' => 'Ansøgninger', 'forms' => $forms, 'applied' => $applied]);
    }

    public function apply(string $slug): void {
        $this->requireAuth();

        $form = db()->fetch(
            'SELECT * FROM ' . DB_PREFIX . 'app_forms WHERE slug = ? AND active = 1',
            [$slug]
        );
        if (!$form) { http_response_code(404); return; }

        if ($form['one_per_user']) {
            $existing = db()->fetch(
                'SELECT id FROM ' . DB_PREFIX . 'applications WHERE form_id = ? AND user_id = ?',
                [$form['id'], auth()->user()['id']]
            );
            if ($existing) {
                flash('error', 'Du har allerede sendt en ansøgning til denne formular.');
                redirect('my-applications');
            }
        }

        $questions = db()->fetchAll(
            'SELECT * FROM ' . DB_PREFIX . 'app_questions WHERE form_id = ? ORDER BY sort_order, id',
            [$form['id']]
        );

        $this->render('apply', [
            'pageTitle' => 'Ansøg — ' . $form['name'],
            'form'      => $form,
            'questions' => $questions,
            'error'     => flash('error'),
        ]);
    }

    public function submit(string $slug): void {
        $this->requireAuth();
        if (!csrf_verify()) { flash('error', 'Ugyldig forespørgsel.'); redirect("apply/{$slug}"); }

        $form = db()->fetch(
            'SELECT * FROM ' . DB_PREFIX . 'app_forms WHERE slug = ? AND active = 1',
            [$slug]
        );
        if (!$form) { http_response_code(404); return; }

        $userId = auth()->user()['id'];

        if ($form['one_per_user']) {
            $existing = db()->fetch(
                'SELECT id FROM ' . DB_PREFIX . 'applications WHERE form_id = ? AND user_id = ?',
                [$form['id'], $userId]
            );
            if ($existing) { flash('error', 'Du har allerede ansøgt.'); redirect("apply/{$slug}"); }
        }

        $questions = db()->fetchAll(
            'SELECT * FROM ' . DB_PREFIX . 'app_questions WHERE form_id = ? ORDER BY sort_order, id',
            [$form['id']]
        );

        foreach ($questions as $q) {
            if ($q['required'] && trim($_POST['q_' . $q['id']] ?? '') === '') {
                flash('error', 'Udfyld alle obligatoriske felter.');
                redirect("apply/{$slug}");
            }
        }

        $now   = date('Y-m-d H:i:s');
        $appId = db()->insert(DB_PREFIX . 'applications', [
            'form_id'    => $form['id'],
            'user_id'    => $userId,
            'status'     => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach ($questions as $q) {
            db()->insert(DB_PREFIX . 'app_answers', [
                'application_id' => $appId,
                'question_id'    => $q['id'],
                'answer'         => trim($_POST['q_' . $q['id']] ?? ''),
            ]);
        }

        flash('success', 'Din ansøgning er sendt! Vi vender tilbage til dig.');
        redirect('my-applications');
    }

    public function mine(): void {
        $this->requireAuth();

        $applications = db()->fetchAll(
            'SELECT a.*, f.name AS form_name
             FROM ' . DB_PREFIX . 'applications a
             JOIN ' . DB_PREFIX . 'app_forms f ON a.form_id = f.id
             WHERE a.user_id = ?
             ORDER BY a.created_at DESC',
            [auth()->user()['id']]
        );

        $this->render('my-applications', [
            'pageTitle'    => 'Mine ansøgninger',
            'applications' => $applications,
            'success'      => flash('success'),
            'error'        => flash('error'),
        ]);
    }
}
