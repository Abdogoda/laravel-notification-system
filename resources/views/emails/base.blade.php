<!DOCTYPE html>
<html lang="{{ $locale ?? 'ar' }}" dir="{{ ($locale ?? 'ar') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0f172a;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #f8fafc;
        }
        .wrapper {
            max-width: 600px;
            margin: 30px auto;
            background: #1e293b;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #334155;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        .header {
            background-color: #0f172a;
            padding: 28px;
            text-align: center;
            border-bottom: 1px solid #334155;
        }
        .header-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .ag-logo {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #18181b;
            border: 2px solid #38bdf8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            font-weight: 900;
            font-size: 18px;
            letter-spacing: -1px;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.4);
        }
        .brand-title {
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }
        .content {
            padding: 32px 24px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #38bdf8;
            margin-bottom: 16px;
        }
        .body-text {
            font-size: 15px;
            line-height: 1.6;
            color: #e2e8f0;
            margin-bottom: 24px;
        }
        .line-item {
            padding: 10px 14px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            margin-bottom: 8px;
            font-size: 14px;
            color: #cbd5e1;
        }
        .footer {
            background-color: #0f172a;
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #334155;
        }
        .footer a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <a href="https://abdogoda.github.io/AG/" target="_blank" class="header-brand">
                <div class="ag-logo">AG</div>
                <span class="brand-title">{{ config('app.name', 'Tharaa El-Alm') }}</span>
            </a>
        </div>
        <div class="content">
            @if(!empty($greeting))
                <div class="greeting">{{ $greeting }}</div>
            @endif
            
            <div class="body-text">
                {{ $body }}
            </div>

            @if(!empty($emailLines) && is_array($emailLines))
                <div style="margin-top: 16px;">
                    @foreach($emailLines as $line)
                        <div class="line-item">{{ $line }}</div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="footer">
            <p style="margin: 0 0 8px 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('front.all_rights_reserved') ?? 'All rights reserved.' }}</p>
            <p style="margin: 0;">
                Powered by <a href="https://abdogoda.github.io/AG/" target="_blank">Abdulrhman Goda (AG)</a>
            </p>
        </div>
    </div>
</body>
</html>
