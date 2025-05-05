<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .email-header {
            background: #20B2AA;
            padding: 20px;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 15px;
        }

        .logo {
            width: 180px;
            height: auto;
        }

        .email-content {
            padding: 30px;
            background: white;
        }

        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
            border-top: 1px solid #dee2e6;
        }

        .highlight-box {
            background: #f8f9fa;
            border-left: 4px solid #20B2AA;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }

        h1, h2, h3 {
            color: #20B2AA;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background: #f8f9fa;
            font-weight: bold;
            text-align: left;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper {
                margin: 0;
                border-radius: 0;
            }

            .email-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="logo-container">
                <img src="{{ asset('storage/jorn.png') }}" alt="{{ config('app.name') }}" class="logo">
            </div>
        </div>

        <div class="email-content">
            @yield('content')
        </div>

        <div class="email-footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>