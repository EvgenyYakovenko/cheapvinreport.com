<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('mail.report.subject', ['vin' => $vin]) }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f2f4f7;
        }
        .email-wrapper {
            padding: 40px 20px;
        }
        .app-name {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .email-body p {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #555555;
            line-height: 1.6;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #2d3748;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #1a202c;
        }
        .divider {
            height: 1px;
            background-color: #dee2e6;
            margin: 25px 0;
        }
        .backup-link {
            font-size: 14px;
            color: #6c757d;
        }
        .backup-link a {
            color: #007bff;
            word-break: break-all;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="app-name">{{ config('app.name', 'VINForSale') }}</div>
        
        <div class="email-container">
            <div class="email-body">
                @if ($userName)
                    <p>{{ __('mail.report.greeting_named', ['name' => $userName]) }}</p>
                @else
                    <p>{{ __('mail.report.greeting') }}</p>
                @endif

                <p>{{ __('mail.report.intro') }}</p>

                <p>
                    <strong>{{ __('mail.report.vin_label') }}</strong> {{ $vin }}<br>
                    <strong>{{ __('mail.report.report_type_label') }}</strong> {{ ucfirst(str_replace('_', ' ', $reportType)) }}
                </p>

                <p>{{ __('mail.report.cta_intro') }}</p>

                <div class="button-container">
                    <a href="{{ $reportUrl }}" class="button">{{ __('mail.report.cta_button') }}</a>
                </div>

                <p style="font-size: 14px; color: #6c757d; margin-top: 20px;">
                    {{ __('mail.report.cta_note') }}
                </p>

                <p>{{ __('mail.report.signature') }}<br>{{ config('app.name', 'VINForSale') }}</p>

                <div class="divider"></div>

                <p class="backup-link">
                    {{ __('mail.report.backup_intro') }}<br>
                    <a href="{{ $reportUrl }}">{{ $reportUrl }}</a>
                </p>
            </div>
        </div>

        <div class="email-footer">
            {{ __('mail.report.footer', ['year' => date('Y'), 'app' => config('app.name', 'VINForSale')]) }}
        </div>
    </div>
</body>
</html>
