<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor PO Acknowledgement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            color: #1f2937;
            margin: 0;
            padding: 24px;
        }

        .wrap {
            max-width: 680px;
            margin: 48px auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .ok {
            color: #166534;
        }

        .bad {
            color: #991b1b;
        }

        .meta {
            margin-top: 14px;
            color: #4b5563;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <h2 class="{{ $success ? 'ok' : 'bad' }}">{{ $title }}</h2>
        <p>{{ $message }}</p>
        @if (!empty($poNumber))
            <p class="meta"><strong>Reference:</strong> {{ $poNumber }}</p>
        @endif
    </div>
</body>

</html>
