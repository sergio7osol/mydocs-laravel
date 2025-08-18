<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>403 Forbidden — MyDocs</title>
  <style>
    :root { color-scheme: light dark; }
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; }
    .wrap { max-width: 720px; margin: 8vh auto; padding: 24px; }
    .card { border: 1px solid rgba(0,0,0,.08); border-radius: 12px; padding: 28px; background: rgba(255,255,255,.6); backdrop-filter: blur(2px); }
    h1 { margin: 0 0 6px; font-size: 28px; }
    .lead { margin: 0 0 18px; color: #666; font-size: 15px; }
    .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .btn { display: inline-block; padding: 9px 14px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }
    .btn-primary { background: #3a5a94; color: #fff; }
    .btn-secondary { background: #f0f0f0; color: #222; }
    @media (prefers-color-scheme: dark) {
      .card { background: rgba(30,30,30,.6); border-color: rgba(255,255,255,.1); }
      .lead { color: #aaa; }
      .btn-secondary { background: #2b2b2b; color: #eee; }
    }
    .meta { margin-top: 12px; color: #888; font-size: 13px; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>403 — Forbidden</h1>
      <p class="lead">You don’t have permission to access this page or perform this action.</p>

      <div class="actions">
        <a class="btn btn-primary" href="{{ route('documents.index') }}">All Documents</a>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">Go Back</a>
        @guest
          <a class="btn btn-secondary" href="{{ route('auth.login', ['return' => request()->fullUrl()]) }}">Sign in and try again</a>
        @endguest
      </div>

      <p class="meta">If you believe this is an error, please contact your administrator.</p>
    </div>
  </div>
</body>
</html>
