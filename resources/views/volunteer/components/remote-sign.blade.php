<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yota Remote Signature Pad</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #F8FAFC;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased p-4">

    <div x-data="remoteSigner()" class="flex-1 flex flex-col justify-between max-w-md mx-auto w-full bg-white shadow-xl rounded-3xl p-6 border border-gray-100 my-auto">
        
        <!-- Header -->
        <div class="text-center">
            <div class="w-16 h-16 bg-lime-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Remote Signature Pad</h1>
            <p class="text-sm text-slate-400 mt-1">Silakan tanda tangan di bawah menggunakan layar sentuh Anda.</p>
        </div>

        <!-- Signature Canvas Box -->
        <div class="my-6 relative bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl p-2 flex-1 flex flex-col min-h-[300px]">
            <canvas x-ref="canvas" class="flex-1 bg-white rounded-xl shadow-inner touch-none border border-slate-100 cursor-crosshair"></canvas>
            <div class="absolute bottom-4 right-4 bg-slate-800/65 backdrop-blur-md px-3 py-1.5 rounded-full text-[10px] text-white pointer-events-none">
                Layar Sentuh Aktif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col gap-3">
            <button @click="clearCanvas" type="button" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-3.5 rounded-2xl text-sm font-semibold transition active:scale-95 shadow-sm">
                Bersihkan Pad
            </button>
            <button @click="submitSignature" type="button" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white py-3.5 rounded-2xl text-sm font-bold transition active:scale-95 shadow-md flex justify-center items-center gap-2">
                <span x-show="!submitting">Kirim Tanda Tangan</span>
                <span x-show="submitting" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
            </button>
        </div>

        <!-- Success Toast Modal -->
        <div x-show="success" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-50 flex items-center justify-center p-6" x-transition>
            <div class="bg-white rounded-3xl p-6 text-center max-w-sm w-full shadow-2xl border">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Tanda Tangan Terkirim!</h3>
                <p class="text-sm text-slate-400 mt-2">Tanda tangan Anda berhasil terkirim ke layar desktop. Anda sekarang dapat menutup halaman ini.</p>
            </div>
        </div>

    </div>

    <script>
        function remoteSigner() {
            return {
                canvas: null,
                ctx: null,
                drawing: false,
                submitting: false,
                success: false,
                token: '{{ $token }}',

                init() {
                    this.canvas = this.$refs.canvas;
                    this.ctx = this.canvas.getContext('2d');
                    
                    // Set correct width/height based on display size
                    this.resizeCanvas();
                    window.addEventListener('resize', () => this.resizeCanvas());

                    // Configure brush style
                    this.ctx.lineWidth = 3;
                    this.ctx.lineCap = 'round';
                    this.ctx.strokeStyle = '#1b1b18';

                    // Mouse Events
                    this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
                    this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                    this.canvas.addEventListener('mouseup', () => this.stopDrawing());
                    
                    // Touch Events
                    this.canvas.addEventListener('touchstart', (e) => this.startDrawing(e));
                    this.canvas.addEventListener('touchmove', (e) => this.draw(e));
                    this.canvas.addEventListener('touchend', () => this.stopDrawing());
                },

                resizeCanvas() {
                    const rect = this.canvas.getBoundingClientRect();
                    this.canvas.width = rect.width;
                    this.canvas.height = rect.height;
                    
                    // Keep brush configuration after resizing
                    if (this.ctx) {
                        this.ctx.lineWidth = 3;
                        this.ctx.lineCap = 'round';
                        this.ctx.strokeStyle = '#1b1b18';
                    }
                },

                getCoords(e) {
                    const rect = this.canvas.getBoundingClientRect();
                    if (e.touches && e.touches.length > 0) {
                        return {
                            x: e.touches[0].clientX - rect.left,
                            y: e.touches[0].clientY - rect.top
                        };
                    }
                    return {
                        x: e.clientX - rect.left,
                        y: e.clientY - rect.top
                    };
                },

                startDrawing(e) {
                    e.preventDefault();
                    this.drawing = true;
                    const coords = this.getCoords(e);
                    this.ctx.beginPath();
                    this.ctx.moveTo(coords.x, coords.y);
                },

                draw(e) {
                    if (!this.drawing) return;
                    e.preventDefault();
                    const coords = this.getCoords(e);
                    this.ctx.lineTo(coords.x, coords.y);
                    this.ctx.stroke();
                },

                stopDrawing() {
                    this.drawing = false;
                },

                clearCanvas() {
                    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                },

                submitSignature() {
                    // Check if canvas is empty before submitting
                    const blank = document.createElement('canvas');
                    blank.width = this.canvas.width;
                    blank.height = this.canvas.height;
                    if (this.canvas.toDataURL() === blank.toDataURL()) {
                        alert('Silakan tanda tangan terlebih dahulu.');
                        return;
                    }

                    this.submitting = true;
                    const base64 = this.canvas.toDataURL();

                    fetch(`/signature/save-remote/${this.token}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            signature_data: base64
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.submitting = false;
                        if (data.message) {
                            this.success = true;
                        } else {
                            alert('Gagal mengirim tanda tangan.');
                        }
                    })
                    .catch(err => {
                        this.submitting = false;
                        alert('Terjadi kesalahan jaringan.');
                    });
                }
            }
        }
    </script>
</body>
</html>
