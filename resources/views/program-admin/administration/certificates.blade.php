<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Sertifikat Relawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F1F5F9; }
        @media print {
            body { background-color: #ffffff; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="p-6 flex flex-col items-center justify-center min-h-screen">

    <div class="no-print mb-6">
        <button onclick="window.print()" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-6 py-3 rounded-full shadow-lg transition transform active:scale-95">
            Cetak Sertifikat / Simpan PDF
        </button>
    </div>

    <!-- Certificate preview frame -->
    <div class="print:m-0">
        {!! $certHtml !!}
    </div>

</body>
</html>
