<div x-data="signatureEngine()" class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
    <div class="flex border-b border-gray-100 pb-3 mb-4 gap-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
        <button type="button" @click="setMode('draw')" :class="mode === 'draw' ? 'text-[#4CAF50] border-b-2 border-[#4CAF50] pb-3' : ''" class="transition">
            Draw Signature
        </button>
        <button type="button" @click="setMode('upload')" :class="mode === 'upload' ? 'text-[#4CAF50] border-b-2 border-[#4CAF50] pb-3' : ''" class="transition">
            Upload Image
        </button>
        <button type="button" @click="setMode('qr')" :class="mode === 'qr' ? 'text-[#4CAF50] border-b-2 border-[#4CAF50] pb-3' : ''" class="transition">
            Sign via QR Code
        </button>
    </div>

    <!-- Hiddens to pass values to form submission -->
    <input type="hidden" name="signature_base64" x-model="signatureData">

    <!-- DRAW MODE -->
    <div x-show="mode === 'draw'" class="space-y-3">
        <div class="border border-gray-200 rounded-xl overflow-hidden bg-slate-50">
            <canvas x-ref="padCanvas" class="w-full h-32 bg-white touch-none cursor-crosshair"></canvas>
        </div>
        <div class="flex justify-between items-center text-xs">
            <button type="button" @click="clearDraw" class="text-red-500 font-semibold hover:underline">Clear Canvas</button>
            <span class="text-gray-400">Use mouse or touch to sign above</span>
        </div>
    </div>

    <!-- UPLOAD MODE -->
    <div x-show="mode === 'upload'" class="space-y-3">
        <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl p-4 bg-slate-50 hover:bg-slate-100 transition relative">
            <input type="file" @change="handleFileUpload" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-xs text-gray-600 font-semibold" x-text="fileName || 'Click or drag signature file here'"></span>
            <span class="text-[10px] text-gray-400 mt-1">PNG, JPG up to 2MB</span>
        </div>
    </div>

    <!-- QR MODE -->
    <div x-show="mode === 'qr'" class="flex flex-col items-center py-4 text-center">
        <div x-show="!qrCreated" class="space-y-3">
            <p class="text-xs text-gray-400 max-w-xs">Generate a temporary QR code to sign directly on your mobile device.</p>
            <button type="button" @click="initQrSession" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                Generate QR Code
            </button>
        </div>

        <div x-show="qrCreated" class="flex flex-col items-center space-y-3">
            <div class="p-3 bg-white border rounded-xl shadow-sm">
                <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent(signingUrl)" alt="Signing QR" class="w-36 h-36">
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                <span class="animate-ping w-2 h-2 rounded-full bg-yellow-400"></span>
                <span x-text="qrStatus">Waiting for signature...</span>
            </div>
            <p class="text-[10px] text-gray-400">Scan QR above with your smartphone camera</p>
        </div>
    </div>

    <!-- Preview of captured signature -->
    <div x-show="signatureData" class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="border rounded-lg p-1 bg-white">
                <img :src="signatureData" alt="Signature Preview" class="h-10 w-24 object-contain">
            </div>
            <div>
                <span class="block text-xs font-bold text-gray-800">Signature captured</span>
                <span class="block text-[10px] text-green-500 font-medium">Ready to submit</span>
            </div>
        </div>
        <button type="button" @click="resetSignature" class="text-xs font-semibold text-red-500 hover:underline">Reset</button>
    </div>
</div>

<script>
    function signatureEngine() {
        return {
            mode: 'draw',
            signatureData: '',
            fileName: '',
            canvas: null,
            ctx: null,
            drawing: false,
            qrCreated: false,
            signingUrl: '',
            statusUrl: '',
            qrStatus: 'Waiting...',
            pollInterval: null,

            init() {
                this.$nextTick(() => {
                    this.initCanvas();
                });
            },

            setMode(mode) {
                this.mode = mode;
                if (mode === 'draw') {
                    this.$nextTick(() => {
                        this.initCanvas();
                    });
                } else {
                    this.stopPolling();
                }
            },

            initCanvas() {
                this.canvas = this.$refs.padCanvas;
                if (!this.canvas) return;
                
                this.ctx = this.canvas.getContext('2d');
                
                // Set canvas resolution
                const rect = this.canvas.getBoundingClientRect();
                this.canvas.width = rect.width;
                this.canvas.height = rect.height;

                this.ctx.lineWidth = 2.5;
                this.ctx.lineCap = 'round';
                this.ctx.strokeStyle = '#1b1b18';

                // Mouse listeners
                this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
                this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                this.canvas.addEventListener('mouseup', () => this.stopDrawing());
                
                // Touch listeners
                this.canvas.addEventListener('touchstart', (e) => this.startDrawing(e));
                this.canvas.addEventListener('touchmove', (e) => this.draw(e));
                this.canvas.addEventListener('touchend', () => this.stopDrawing());
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
                this.updateDataUrl();
            },

            stopDrawing() {
                this.drawing = false;
            },

            clearDraw() {
                if (this.ctx) {
                    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                    this.signatureData = '';
                }
            },

            updateDataUrl() {
                this.signatureData = this.canvas.toDataURL();
            },

            handleFileUpload(e) {
                const file = e.target.files[0];
                if (!file) return;

                this.fileName = file.name;
                const reader = new FileReader();
                reader.onload = (event) => {
                    this.signatureData = event.target.result;
                };
                reader.readAsDataURL(file);
            },

            initQrSession() {
                fetch('/signature/session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.signingUrl = data.signing_url;
                    this.statusUrl = data.status_url;
                    this.qrCreated = true;
                    this.qrStatus = 'Waiting for mobile signature...';
                    this.startPolling();
                });
            },

            startPolling() {
                this.stopPolling();
                this.pollInterval = setInterval(() => {
                    fetch(this.statusUrl)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'completed') {
                            this.signatureData = data.signature_data;
                            this.qrStatus = 'Signature received!';
                            this.stopPolling();
                        } else if (data.status === 'expired') {
                            this.qrStatus = 'QR Code expired. Try again.';
                            this.stopPolling();
                        }
                    });
                }, 2000);
            },

            stopPolling() {
                if (this.pollInterval) {
                    clearInterval(this.pollInterval);
                    this.pollInterval = null;
                }
            },

            resetSignature() {
                this.signatureData = '';
                this.fileName = '';
                this.qrCreated = false;
                this.clearDraw();
                this.stopPolling();
            }
        }
    }
</script>
