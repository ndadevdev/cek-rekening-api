<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Work Sans', -apple-system, 'Segoe UI', Helvetica, sans-serif;
            background: #FAFAFA; color: #0A0A0A; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .gate-box { max-width: 340px; width: 90%; text-align: center; }
        .gate-box h1 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: #A3A3A3; margin-bottom: 4px; }
        .gate-box h2 { font-family: 'Archivo Black', Impact, 'Arial Black', sans-serif; font-size: 28px; letter-spacing: -0.02em; margin-bottom: 20px; }
        .field { text-align: left; margin-bottom: 16px; }
        .field label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 6px; }
        .field input {
            width: 100%; height: 44px; border: 2px solid #D4D4D4; background: #FAFAFA;
            padding: 8px 14px; font-family: 'Work Sans', sans-serif; font-size: 14px;
            outline: none;
        }
        .field input:focus { border-color: #0A0A0A; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            height: 44px; padding: 0 24px; border: 2px solid #0A0A0A; width: 100%;
            font-family: 'Work Sans', sans-serif; font-size: 13px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em; cursor: pointer;
            background: #0A0A0A; color: #FAFAFA; transition: background .15s;
        }
        .btn:hover { background: #EF4444; border-color: #EF4444; }
        .error-msg { font-size: 12px; color: #EF4444; margin-top: 8px; }
        .hidden { display: none !important; }
    </style>
</head>
<body>
    <div class="gate-box">
        <h1>Akses Demo</h1>
        <h2 class="display">Masukkan Kode</h2>
        <form method="POST">
            @csrf
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" autofocus>
            </div>
            <button type="submit" class="btn">Masuk</button>
            @if ($errors->any())
                <div class="error-msg">{{ $errors->first() }}</div>
            @endif
        </form>
    </div>
</body>
</html>
