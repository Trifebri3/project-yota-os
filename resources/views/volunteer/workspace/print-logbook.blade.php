<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook Harian - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #334155;
            margin: 30px;
            line-height: 1.5;
        }
        .header-letterhead {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #334155;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .logo-box {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #f8fafc;
        }
        .logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .letterhead-title {
            text-align: center;
            flex-1: 1;
            padding: 0 20px;
        }
        .letterhead-title h1 {
            font-size: 18px;
            margin: 0;
            color: #0f172a;
            font-family: Georgia, serif;
            text-transform: uppercase;
        }
        .letterhead-title p {
            margin: 4px 0 0 0;
            font-size: 10px;
            color: #64748b;
            font-weight: 500;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .meta-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .meta-table td.label {
            width: 150px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            color: #64748b;
            background: #f1f5f9;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .log-table th, .log-table td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }
        .log-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 9px;
        }
        .status-badge {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid;
            display: inline-block;
        }
        .status-approved { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
        .status-pending { background: #fef9c3; color: #854d0e; border-color: #fef08a; }
        .status-rejected { background: #fef2f2; color: #991b1b; border-color: #fca5a5; }
        
        .photo-evidence {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-top: 5px;
            display: block;
        }
        .footer-signatures {
            margin-top: 50px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        .sig-box {
            text-align: center;
            width: 220px;
        }
        .sig-space {
            height: 80px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            color: #94a3b8;
        }
        .qr-placeholder {
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-size: 8px;
            background: #f8fafc;
            display: inline-block;
            margin-top: 15px;
            border-radius: 8px;
        }
        
        /* Interactive Date Filter Form */
        .filter-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 16px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .filter-inputs {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filter-inputs input {
            padding: 6px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 11px;
            outline: none;
        }
        .filter-inputs input:focus {
            border-color: #4CAF50;
        }
        .btn-action {
            font-weight: bold;
            cursor: pointer;
            border-radius: 8px;
            border: none;
            padding: 8px 16px;
            font-size: 11px;
            transition: all 0.2s;
        }
        .btn-filter {
            background: #4CAF50;
            color: white;
        }
        .btn-filter:hover {
            background: #388e3c;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm 12mm 15mm 12mm;
            }
            .no-print { display: none !important; }
            body { 
                margin: 0; 
                padding: 0;
                font-size: 10px;
                background: #fff;
                color: #000;
            }
            .meta-table { background: transparent; }
            .footer-signatures { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <!-- Filter & Controls -->
    <div class="no-print filter-banner">
        <form method="GET" action="{{ route('volunteer.workspace.logbooks.print') }}" class="filter-inputs">
            <div>
                <label style="font-weight: bold; font-size: 9px; display: block; color: #64748b; text-transform: uppercase;">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div>
                <label style="font-weight: bold; font-size: 9px; display: block; color: #64748b; text-transform: uppercase;">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <button type="submit" class="btn-action btn-filter" style="margin-top: 13px;">Filter & Update</button>
            <a href="{{ route('volunteer.workspace.logbooks.print') }}" class="btn-action" style="background: #e2e8f0; color: #334155; margin-top: 13px; text-decoration: none; display: inline-block; text-align: center;">Reset Filter</a>
        </form>

        <button onclick="window.print()" class="btn-action" style="background: #0f172a; color: white;">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <!-- Letterhead -->
    <div class="header-letterhead">
        <!-- Left: Yota OS Logo -->
        <div class="logo-box" title="Logo Yota OS">
            <img src="/images/logo.webp" alt="Yota OS Logo">
        </div>

        <!-- Center text -->
        <div class="letterhead-title">
            <h1>Yota Project OS</h1>
            <p>Community Development & Social Project Operating System</p>
            <p style="font-size: 11px; margin-top: 8px; letter-spacing: 1px; color: #0f172a; font-weight: bold;">
                JOURNAL LOGBOOK PEKERJAAN SOSIAL & RELAWAN
            </p>
        </div>

        <!-- Right: Program Logo -->
        <div class="logo-box" title="Logo Program">
            @if($program && $program->logo_program)
                <img src="{{ $program->logo_program }}" alt="Program Logo">
            @else
                <img src="/images/logo.webp" alt="Program Logo Placeholder">
            @endif
        </div>
    </div>

    <!-- Identity metadata -->
    <table class="meta-table">
        <tr>
            <td class="label">Nama Relawan:</td>
            <td>{{ $user->name }}</td>
            <td class="label">Program Utama:</td>
            <td><strong>{{ $program->name ?? 'Program Umum' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Institusi:</td>
            <td>{{ $user->organisasi ?? 'Umum' }}</td>
            <td class="label">Periode Filter:</td>
            <td>
                @if(request('start_date') || request('end_date'))
                    {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} s/d {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : 'Akhir' }}
                @else
                    Semua Periode Program
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Email & Kontak:</td>
            <td>{{ $user->email }} / {{ $user->phone ?? '-' }}</td>
            <td class="label">Tanggal Cetak:</td>
            <td>{{ date('d F Y H:i T') }}</td>
        </tr>
    </table>

    <!-- Logs Table -->
    <table class="log-table">
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 25%;">Aktivitas / Kegiatan</th>
                <th style="width: 35%;">Uraian Kerja & Deskripsi Hasil</th>
                <th style="width: 8%; text-align: center;">Durasi</th>
                <th style="width: 12%; text-align: center;">Bukti Foto</th>
                <th style="width: 8%; text-align: center;">Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logbooks as $log)
                <tr>
                    <td style="font-weight: 500;">{{ $log->date->format('d/m/Y') }}</td>
                    <td><strong>{{ $log->title }}</strong></td>
                    <td style="white-space: pre-line;">{{ $log->content }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $log->hours_spent }} Jam</td>
                    <td style="text-align: center;">
                        @if($log->photo_path)
                            <a href="{{ $log->photo_path ? (str_starts_with($log->photo_path, 'http') ? $log->photo_path : (str_starts_with($log->photo_path, '/storage/') ? $log->photo_path : (str_starts_with($log->photo_path, 'storage/') ? '/' . $log->photo_path : '/storage/' . ltrim($log->photo_path, '/')))) : '' }}" target="_blank">
                                <img src="{{ $log->photo_path ? (str_starts_with($log->photo_path, 'http') ? $log->photo_path : (str_starts_with($log->photo_path, '/storage/') ? $log->photo_path : (str_starts_with($log->photo_path, 'storage/') ? '/' . $log->photo_path : '/storage/' . ltrim($log->photo_path, '/')))) : '' }}" class="photo-evidence">
                            </a>
                        @else
                            <span style="color: #94a3b8; font-style: italic; font-size: 9px;">Tanpa Foto</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($log->status === 'approved')
                            <span class="status-badge status-approved">Approved</span>
                        @elseif($log->status === 'pending')
                            <span class="status-badge status-pending">Pending</span>
                        @else
                            <span class="status-badge status-rejected">Rejected</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            @if($logbooks->isEmpty())
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; font-style: italic; color: #94a3b8;">
                        Tidak ada logbook ditemukan pada filter tanggal ini.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Signature block -->
    <div class="footer-signatures">
        <div class="sig-box">
            <p>Relawan / Pelaksana,</p>
            <div class="sig-space" style="display: flex; justify-content: center; align-items: center; min-height: 60px;">
                @if($user->signature_path)
                    <img src="{{ $user->signature_path ? (str_starts_with($user->signature_path, 'http') ? $user->signature_path : (str_starts_with($user->signature_path, '/storage/') ? $user->signature_path : (str_starts_with($user->signature_path, 'storage/') ? '/' . $user->signature_path : '/storage/' . ltrim($user->signature_path, '/')))) : '' }}" style="max-height: 55px; object-fit: contain; mix-blend-mode: multiply;">
                @else
                    <div style="height: 55px; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-style: italic; font-size: 8px;">(Belum Upload TTD)</div>
                @endif
            </div>
            <p><strong>{{ $user->name }}</strong></p>
            <p style="font-size: 9px; color: #64748b; margin: 2px 0 0 0;">ID: {{ substr($user->id, 0, 8) }}</p>
        </div>

        <div style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 150px;">
            <div style="border: 1px solid #cbd5e1; padding: 6px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: inline-block;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode('http://yota.os/verify/' . substr($user->id, 0, 8)) }}" style="width: 80px; height: 80px; display: block;">
            </div>
            <strong style="font-size: 8px; color: #1e293b; display: block; margin-top: 4px; letter-spacing: 0.5px;">QR VALIDATION</strong>
            <span style="font-family: monospace; font-size: 7px; color: #64748b; display: block; margin-top: 1px;">yota.os/verify/{{ substr($user->id, 0, 8) }}</span>
        </div>

        <div class="sig-box">
            <p>Mengesahkan,<br>Project Officer,</p>
            <div class="sig-space" style="display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 60px;">
                @php
                    $signer = null;
                    if ($program) {
                        $signer = $program->projectOfficer ?? $program->supervisor ?? null;
                    }
                    if (!$signer) {
                        $signer = \App\Models\User::where('role', 'program-admin')->first();
                    }
                    
                    $roleTitle = 'Project Officer';
                    if ($signer) {
                        if ($signer->role === 'project-officer') {
                            $roleTitle = 'Project Officer';
                        } elseif ($signer->role === 'program-admin') {
                            $roleTitle = 'Direktur Program Yota Adiwidya Center';
                        } elseif ($signer->role === 'supervisor') {
                            $roleTitle = 'Supervisor';
                        }
                    }
                @endphp
                @if($signer && $signer->signature_path)
                    <img src="{{ $signer->signature_path ? (str_starts_with($signer->signature_path, 'http') ? $signer->signature_path : (str_starts_with($signer->signature_path, '/storage/') ? $signer->signature_path : (str_starts_with($signer->signature_path, 'storage/') ? '/' . $signer->signature_path : '/storage/' . ltrim($signer->signature_path, '/')))) : '' }}" style="max-height: 50px; object-fit: contain; mix-blend-mode: multiply; margin-bottom: 4px;">
                @else
                    <div style="height: 50px; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-style: italic; font-size: 8px;">(Belum Upload TTD)</div>
                @endif
            </div>
            <p><strong>{{ $signer?->name ?? 'Admin Program Yota' }}</strong></p>
            <p style="font-size: 9px; color: #64748b; margin: 2px 0 0 0;">{{ $roleTitle }}</p>
        </div>
    </div>

</body>
</html>
