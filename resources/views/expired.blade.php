<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Akun Ditangguhkan - {{ config('app.name', 'Komang') }}</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Segoe UI', system-ui, -apple-system, Roboto, 'Helvetica Neue', sans-serif;
                background: linear-gradient(160deg, #e8f0fe 0%, #f5f6f8 100%);
                min-height: 100dvh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
            }
            .card {
                background: #ffffff;
                border-radius: 16px;
                max-width: 400px;
                width: 100%;
                padding: 40px 24px;
                text-align: center;
                box-shadow: 0 12px 40px rgba(30, 136, 229, 0.12);
            }
            .icon {
                width: 64px;
                height: 64px;
                margin: 0 auto 20px;
                border-radius: 50%;
                background: #fff3e0;
                color: #ef6c00;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 34px;
                font-weight: 700;
            }
            h1 {
                font-size: 20px;
                color: #1c2438;
                margin-bottom: 12px;
            }
            p {
                font-size: 14px;
                line-height: 1.6;
                color: #6b7280;
                margin-bottom: 8px;
            }
            .date {
                display: inline-block;
                margin-top: 16px;
                background: #f3f4f6;
                color: #374151;
                border-radius: 999px;
                padding: 8px 16px;
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="icon">!</div>
            <h1>Aplikasi Sedang Ditangguhkan</h1>
            <p>{{ $message }}</p>
            <div class="date">Berlaku sejak {{ $expiredAt }}</div>
        </div>
    </body>
</html>
