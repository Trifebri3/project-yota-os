<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Sertifikat - Yota OS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="max-w-md w-full bg-white shadow-xl rounded-3xl p-6 border border-slate-100 text-center space-y-6">
        
        <!-- Validation success checkmark -->
        <div class="w-20 h-20 bg-green-50 border-4 border-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto animate-bounce">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <div class="space-y-1">
            <h1 class="text-xl font-extrabold text-slate-900 tracking-wide">Sertifikat Terverifikasi</h1>
            <p class="text-xs text-green-600 font-semibold uppercase tracking-wider">Validitas Keaslian: 100% Asli</p>
        </div>

        <!-- Verification credentials details -->
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-150 text-left text-xs space-y-2.5">
            <div class="flex justify-between border-b pb-1.5">
                <span class="text-gray-400 font-medium">No. Sertifikat:</span>
                <strong class="text-slate-800 font-mono">{{ $certificate->certificate_number }}</strong>
            </div>
            <div class="flex justify-between border-b pb-1.5">
                <span class="text-gray-400 font-medium">Nama Penerima:</span>
                <strong class="text-slate-800">{{ $certificate->user?->name }}</strong>
            </div>
            <div class="flex justify-between border-b pb-1.5">
                <span class="text-gray-400 font-medium">Program Kerja:</span>
                <strong class="text-slate-800 text-right max-w-[200px] truncate" title="{{ $certificate->program?->name }}">{{ $certificate->program?->name }}</strong>
            </div>
            <div class="flex justify-between border-b pb-1.5">
                <span class="text-gray-400 font-medium">Tanggal Rilis:</span>
                <strong class="text-slate-800">{{ $certificate->issue_date?->format('d M Y') }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400 font-medium">Status Relawan:</span>
                <strong class="text-green-650 uppercase font-extrabold">COMPLETED / VERIFIED</strong>
            </div>
        </div>

        <p class="text-[10px] text-gray-400 leading-normal">
            Verifikasi sertifikat ini dilakukan secara otomatis oleh sistem blockchain-like validator YOTA PROJECT OS. Hubungi CSR Admin untuk detail lebih lanjut.
        </p>

        <a href="{{ route('home') }}" class="block text-center bg-slate-900 hover:bg-black text-white text-xs font-bold py-3 rounded-2xl transition shadow-md">
            Kembali ke Portal Publik
        </a>

    </div>

</body>
</html>
