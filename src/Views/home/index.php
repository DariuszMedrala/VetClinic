<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?></title>
  <style>
    body { font-family: system-ui, sans-serif; margin: 0; display: grid;
           place-items: center; min-height: 100vh; background: #0f766e; color: #fff; }
    .card { background: #fff; color: #134e4a; padding: 2.5rem 3rem; border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,.25); max-width: 460px; }
    h1 { margin: 0 0 .25rem; font-size: 1.6rem; }
    p { margin: .35rem 0; color: #475569; }
    code { background: #f1f5f9; padding: 2px 6px; border-radius: 6px; }
    .ok { color: #0f766e; font-weight: 700; }
  </style>
</head>
<body>
  <main class="card">
    <h1>🐾 <?= e($title) ?></h1>
    <p class="ok">Rdzeń MVC działa — żądanie przeszło przez Router i Controller.</p>
    <p>Trasa <code>GET /</code> → <code>HomeController::index</code></p>
  </main>
</body>
</html>
