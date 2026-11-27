<?php
// Relais JSON vers VeilleFacile : la clé reste côté serveur.
require __DIR__ . '/lib/veille.php';
header('Content-Type: application/json; charset=utf-8');

try {
    if (isset($_GET['thumb'])) {
        $id = (int) $_GET['thumb'];
        // Seules les Sources publiques connues peuvent déclencher une résolution de miniature.
        $source = current(array_filter(vf_all_sources()['data'], fn($s) => $s['id'] === $id));
        echo json_encode(['thumb' => $source ? vf_thumb($source) : null]);
        exit;
    }
    echo json_encode(vf_list(
        trim(mb_substr((string) ($_GET['q'] ?? ''), 0, 200)),
        (string) ($_GET['format'] ?? ''),
        mb_substr((string) ($_GET['kw'] ?? ''), 0, 100),
        max(0, (int) ($_GET['offset'] ?? 0)),
        min(100, max(1, (int) ($_GET['limit'] ?? 24)))
    ));
} catch (Throwable $e) {
    http_response_code(502);
    echo json_encode(['error' => 'VeilleFacile est indisponible pour le moment.']);
}
