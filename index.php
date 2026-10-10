<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$mensaje = '';
$error = '';
$editando = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $destinatario = trim((string)($_POST['destinatario'] ?? ''));
    $direccion = trim((string)($_POST['direccion'] ?? ''));
    $descripcion = trim((string)($_POST['descripcion'] ?? ''));

    if ($accion === 'eliminar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM envios WHERE id = ?');
            $stmt->execute([$id]);
            header('Location: index.php?estado=eliminado');
            exit;
        }
        $error = 'Identificador de envío no válido.';
    } elseif (in_array($accion, ['crear', 'actualizar'], true)) {
        if ($destinatario === '' || $direccion === '' || $descripcion === '') {
            $error = 'Completa todos los campos.';
        } elseif (mb_strlen($destinatario) > 150 || mb_strlen($direccion) > 255) {
            $error = 'El destinatario admite hasta 150 caracteres y la dirección hasta 255.';
        } else {
            if ($accion === 'crear') {
                $stmt = $pdo->prepare('INSERT INTO envios (destinatario, direccion, descripcion) VALUES (?, ?, ?)');
                $stmt->execute([$destinatario, $direccion, $descripcion]);
                header('Location: index.php?estado=creado');
                exit;
            } else {
                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                if (!$id) {
                    $error = 'Identificador de envío no válido.';
                } else {
                    $stmt = $pdo->prepare('UPDATE envios SET destinatario = ?, direccion = ?, descripcion = ? WHERE id = ?');
                    $stmt->execute([$destinatario, $direccion, $descripcion, $id]);
                    header('Location: index.php?estado=actualizado');
                    exit;
                }
            }
        }
    }
}

if (isset($_GET['editar'])) {
    $id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM envios WHERE id = ?');
        $stmt->execute([$id]);
        $editando = $stmt->fetch() ?: null;
    }
}

$estados = [
    'creado' => 'Envío registrado correctamente.',
    'actualizado' => 'Envío actualizado correctamente.',
    'eliminado' => 'Envío eliminado correctamente.',
];
if (isset($_GET['estado'], $estados[$_GET['estado']])) {
    $mensaje = $estados[$_GET['estado']];
}

$envios = $pdo->query('SELECT * FROM envios ORDER BY creado_en DESC, id DESC')->fetchAll();
$destinatarioForm = $editando['destinatario'] ?? ($_POST['destinatario'] ?? '');
$direccionForm = $editando['direccion'] ?? ($_POST['direccion'] ?? '');
$descripcionForm = $editando['descripcion'] ?? ($_POST['descripcion'] ?? '');
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gestión de envíos</title>
<style>
:root{--primary:#2457c5;--dark:#17233b;--muted:#68758a;--bg:#f3f6fb;--line:#e0e7f0}
*{box-sizing:border-box}body{margin:0;background:var(--bg);font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:var(--dark)}
header{background:linear-gradient(120deg,#173a8a,#3979e8);color:#fff;padding:28px 20px}
.wrap{max-width:1100px;margin:0 auto;padding:0 18px}header h1{margin:0 0 6px;font-size:clamp(24px,4vw,34px)}header p{margin:0;color:#e6eeff}
main{padding:26px 0 45px}.panel{background:#fff;border:1px solid var(--line);border-radius:16px;padding:22px;margin-bottom:22px;box-shadow:0 8px 24px #17233b0b}
h2{margin:0 0 18px;font-size:20px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.full{grid-column:1/-1}
label{display:block;font-weight:650;font-size:14px;margin-bottom:7px}input,textarea{width:100%;padding:11px 12px;border:1px solid #cbd5e3;border-radius:9px;font:inherit;background:#fff}textarea{min-height:92px;resize:vertical}
.actions{display:flex;gap:9px;flex-wrap:wrap;margin-top:16px}.btn{border:0;border-radius:9px;padding:10px 15px;background:var(--primary);color:#fff;font-weight:650;text-decoration:none;cursor:pointer;display:inline-block;font-size:14px}.btn.secondary{background:#eaf0fb;color:#244477}.btn.danger{background:#b42336}.alert{padding:12px 14px;border-radius:9px;margin-bottom:18px}.success{background:#e9f8ef;color:#17663a}.error{background:#fff0f0;color:#9b1c1c}
.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:650px}th,td{text-align:left;padding:13px 12px;border-bottom:1px solid var(--line);vertical-align:top}th{font-size:12px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);background:#f8faff}td{font-size:14px}td.desc{white-space:pre-wrap;max-width:330px}.inline{display:inline}.date{color:var(--muted);font-size:12px}.empty{text-align:center;color:var(--muted);padding:28px}
@media(max-width:650px){.grid{grid-template-columns:1fr}.full{grid-column:auto}.panel{padding:16px}}
</style>
</head>
<body>
<header><div class="wrap"><h1>Gestión de envíos</h1><p>Registra, consulta, modifica y elimina envíos.</p></div></header>
<main class="wrap">
<?php if ($mensaje): ?><div class="alert success"><?= h($mensaje) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
<section class="panel">
<h2><?= $editando ? 'Editar envío #' . (int)$editando['id'] : 'Registrar un envío' ?></h2>
<form method="post" action="index.php">
<input type="hidden" name="accion" value="<?= $editando ? 'actualizar' : 'crear' ?>">
<?php if ($editando): ?><input type="hidden" name="id" value="<?= (int)$editando['id'] ?>"><?php endif; ?>
<div class="grid">
<div><label for="destinatario">Destinatario</label><input id="destinatario" name="destinatario" maxlength="150" required value="<?= h((string)$destinatarioForm) ?>" placeholder="Nombre de quien recibe"></div>
<div><label for="direccion">Dirección</label><input id="direccion" name="direccion" maxlength="255" required value="<?= h((string)$direccionForm) ?>" placeholder="Dirección completa de entrega"></div>
<div class="full"><label for="descripcion">Descripción</label><textarea id="descripcion" name="descripcion" required placeholder="Contenido o detalles del envío"><?= h((string)$descripcionForm) ?></textarea></div>
</div>
<div class="actions"><button class="btn" type="submit"><?= $editando ? 'Guardar cambios' : 'Registrar envío' ?></button><?php if ($editando): ?><a class="btn secondary" href="index.php">Cancelar</a><?php endif; ?></div>
</form>
</section>

<section class="panel">
<h2>Envíos registrados <span class="date">(<?= count($envios) ?>)</span></h2>
<?php if (!$envios): ?><div class="empty">Todavía no hay envíos registrados. Usa el formulario para crear el primero.</div>
<?php else: ?><div class="table-wrap"><table>
<thead><tr><th>ID</th><th>Destinatario</th><th>Dirección</th><th>Descripción</th><th>Fecha</th><th>Acciones</th></tr></thead>
<tbody><?php foreach ($envios as $envio): ?><tr>
<td><?= (int)$envio['id'] ?></td><td><?= h($envio['destinatario']) ?></td><td><?= h($envio['direccion']) ?></td><td class="desc"><?= h($envio['descripcion']) ?></td><td><?= h($envio['creado_en']) ?></td>
<td><div class="actions" style="margin:0"><a class="btn secondary" href="index.php?editar=<?= (int)$envio['id'] ?>">Editar</a>
<form class="inline" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar este envío?')"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int)$envio['id'] ?>"><button class="btn danger" type="submit">Eliminar</button></form></div></td>
</tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
</section>
</main>
</body></html>
