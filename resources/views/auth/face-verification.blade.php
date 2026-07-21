<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.face_scan_title') }} | {{ __('ui.app_name') }}</title>
    <style>
        :root {
            --bg: #09111f;
            --panel: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #dbe4ef;
            --primary: #2563eb;
            --success: #16a34a;
            --warning: #f59e0b;
            --danger: #dc2626;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 20% 20%, rgba(37, 99, 235, 0.22), transparent 34%),
                radial-gradient(circle at 80% 70%, rgba(20, 184, 166, 0.14), transparent 32%),
                linear-gradient(135deg, #06101f, #101827 58%, #18243a);
            display: grid;
            place-items: center;
            padding: 28px 16px;
        }

        .scan-shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: minmax(0, 1.12fr) minmax(320px, 0.88fr);
            overflow: hidden;
            border-radius: 22px;
            background: var(--panel);
            box-shadow: 0 40px 110px rgba(0, 0, 0, 0.48);
        }

        .camera-panel {
            position: relative;
            min-height: 580px;
            background: #020617;
            overflow: hidden;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
            opacity: 0.88;
        }

        .camera-placeholder {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            color: rgba(255, 255, 255, 0.74);
            text-align: center;
            padding: 32px;
        }

        .scan-frame {
            position: absolute;
            inset: 9%;
            border: 2px solid rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            box-shadow: 0 0 0 999px rgba(2, 6, 23, 0.34), inset 0 0 48px rgba(37, 99, 235, 0.18);
            pointer-events: none;
        }

        .scan-line {
            position: absolute;
            left: 14%;
            right: 14%;
            top: 16%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.95), transparent);
            box-shadow: 0 0 22px rgba(59, 130, 246, 0.9);
            opacity: 0;
            transform: translateY(0);
        }

        .is-scanning .scan-line {
            opacity: 1;
            animation: scan 2.2s ease-in-out infinite;
        }

        @keyframes scan {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(330px); }
        }

        .content-panel {
            padding: 54px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 24px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--primary);
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .brand-mark {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: #fff;
            background: var(--primary);
        }

        h1 {
            font-size: clamp(30px, 4vw, 44px);
            line-height: 1.08;
            font-weight: 800;
        }

        .lead {
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .status {
            min-height: 58px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #f8fafc;
            color: #334155;
            font-size: 14px;
            line-height: 1.45;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: var(--warning);
            box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.13);
            flex: 0 0 auto;
        }

        .status.success .status-dot {
            background: var(--success);
            box-shadow: 0 0 0 5px rgba(22, 163, 74, 0.13);
        }

        .status.error .status-dot {
            background: var(--danger);
            box-shadow: 0 0 0 5px rgba(220, 38, 38, 0.13);
        }

        .server-error {
            padding: 12px 14px;
            border: 1px solid rgba(220, 38, 38, 0.24);
            border-radius: 12px;
            background: #fef2f2;
            color: #991b1b;
            font-size: 14px;
            line-height: 1.5;
        }

        .actions {
            display: grid;
            gap: 12px;
        }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 14px 18px;
            color: #fff;
            background: var(--primary);
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.18s ease, background 0.18s ease, opacity 0.18s ease;
        }

        .btn:hover { background: #1d4ed8; transform: translateY(-1px); }
        .btn:disabled { cursor: not-allowed; opacity: 0.62; transform: none; }

        .privacy {
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        .logout-form {
            margin-top: 8px;
        }

        .link-button {
            border: 0;
            background: transparent;
            color: var(--primary);
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            padding: 0;
        }

        @media (max-width: 840px) {
            .scan-shell { grid-template-columns: 1fr; }
            .camera-panel { min-height: 420px; }
            .content-panel { padding: 34px 24px; }
            .scan-frame { inset: 10% 18%; }
        }
    </style>
</head>
<body>
<main class="scan-shell">
    <section class="camera-panel" id="cameraPanel">
        <video id="camera" playsinline muted></video>
        <div class="camera-placeholder" id="cameraPlaceholder">
            <div>
                <strong>{{ __('ui.face_scan_camera_waiting') }}</strong><br>
                {{ __('ui.face_scan_camera_hint') }}
            </div>
        </div>
        <div class="scan-frame"></div>
        <div class="scan-line"></div>
    </section>

    <section class="content-panel">
        <div class="brand">
            <span class="brand-mark">AI</span>
            <span>{{ __('ui.app_name') }}</span>
        </div>

        <div>
            <h1>{{ __('ui.face_scan_heading') }}</h1>
            <p class="lead">{{ __('ui.face_scan_text') }}</p>
        </div>

        <div class="status" id="scanStatus" role="status" aria-live="polite">
            <span class="status-dot"></span>
            <span id="scanStatusText">{{ __('ui.face_scan_status_ready') }}</span>
        </div>

        @if ($errors->has('face'))
            <div class="server-error">{{ $errors->first('face') }}</div>
        @endif

        <div class="actions">
            <button class="btn" type="button" id="startScan">{{ __('ui.face_scan_start') }}</button>
            <form id="confirmScanForm" action="{{ route('face.verification.confirm') }}" method="POST">
                @csrf
                <input type="hidden" name="face_scan_token" value="{{ $faceScanToken }}">
                <input type="hidden" id="faceSignature" name="face_signature" value="">
            </form>
        </div>

        <p class="privacy">{{ __('ui.face_scan_privacy') }}</p>

        <form class="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="link-button" type="submit">{{ __('ui.nav_logout') }}</button>
        </form>
    </section>
</main>

<script>
    window.FaceVerification = {
        modelUrl: @json(asset('models')),
        messages: {
            cameraStarting: @json(__('ui.face_scan_status_camera_starting')),
            scanning: @json(__('ui.face_scan_status_scanning')),
            faceFound: @json(__('ui.face_scan_status_face_found')),
            success: @json(__('ui.face_scan_status_success')),
            cameraError: @json(__('ui.face_scan_status_camera_error')),
            noFace: @json(__('ui.face_scan_status_no_face')),
            unsupported: @json(__('ui.face_scan_status_unsupported')),
            signatureError: @json(__('ui.face_scan_signature_error')),
        },
    };
</script>
@vite('resources/js/face-verification.js')
</body>
</html>
