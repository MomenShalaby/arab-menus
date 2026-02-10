@extends('layouts.app')

@section('title', ($currentLocale ?? 'ar') === 'ar' ? 'عجلة اختيار المطاعم - اختر بين المطاعم بسهولة | ناكل ايه' : 'Restaurant Picker Wheel - Choose Between Restaurants | Nakol Eh')
@section('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'مش عارف تختار بين المطاعم؟ اكتب أسماء المطاعم ودور العجلة! أداة مجانية لاختيار المطعم بسهولة' : 'Can\'t choose between restaurants? Add restaurant names and spin the wheel! Free tool to pick a restaurant easily')
@section('meta_keywords', 'عجلة اختيار مطعم, picker wheel restaurants, اختيار بين مطاعم, random picker, عجلة الحظ مطاعم')

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "عجلة اختيار المطاعم",
    "description": "أداة مجانية لاختيار المطعم بسهولة عن طريق تدوير العجلة",
    "url": "{{ route('picker-wheel') }}",
    "applicationCategory": "UtilitiesApplication",
    "operatingSystem": "Web"
}
</script>
@endpush

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8 sm:py-12">
        <!-- Header -->
        <div class="text-center mb-8 sm:mb-12">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-800 mb-3 sm:mb-4">عجلة الاختيار 🎡</h1>
            <p class="text-base sm:text-lg text-gray-600">مش عارف تختار بين المطاعم؟ اكتب أسماءهم ودور العجلة!</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            <!-- Wheel Container -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 md:p-8">
                <div class="relative flex flex-col items-center">
                    <!-- Pointer -->
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-2 z-10">
                        <div class="w-0 h-0 border-l-[15px] border-l-transparent border-r-[15px] border-r-transparent border-b-[25px] border-b-red-500 drop-shadow-lg"></div>
                    </div>

                    <!-- Canvas -->
                    <canvas id="wheel-canvas" width="340" height="340" class="drop-shadow-2xl max-w-full h-auto"></canvas>

                    <!-- Spin Button -->
                    <button id="spin-btn"
                        onclick="spinWheel()"
                        class="mt-6 bg-gradient-to-r from-primary-500 to-accent-500 text-white font-bold py-3 px-8 rounded-full hover:from-primary-600 hover:to-accent-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            دوّر!
                        </span>
                    </button>
                </div>

                <!-- Winner Display -->
                <div id="winner-display" class="hidden mt-8 p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 text-center">
                    <h3 class="text-2xl font-extrabold text-green-700 mb-2">🎉 الفائز هو</h3>
                    <p id="winner-text" class="text-3xl font-extrabold text-green-900"></p>
                </div>
            </div>

            <!-- Input Section -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    أضف المطاعم
                </h2>

                <div class="mb-4">
                    <input type="text" id="restaurant-input"
                        placeholder="اكتب اسم مطعم واضغط Enter"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none"
                        onkeypress="handleInputKeypress(event)">
                </div>

                <button onclick="addToWheel()"
                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-xl transition-colors mb-6">
                    أضف للعجلة
                </button>

                <!-- Items List -->
                <div class="mb-4">
                    <h3 class="font-semibold text-gray-700 mb-3">المطاعم على العجلة (<span id="items-count">0</span>/10):</h3>
                    <ul id="items-list" class="space-y-2"></ul>
                </div>

                <button onclick="clearWheel()"
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors text-sm">
                    امسح الكل
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .shake {
            animation: shake 0.3s ease;
        }
    </style>
@endsection

@push('scripts')
<script>
    const canvas = document.getElementById('wheel-canvas');
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = 150;

    let wheelItems = [];
    let rotation = 0;
    let isSpinning = false;

    const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4'];

    function drawWheel() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (wheelItems.length === 0) {
            // Draw empty wheel
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
            ctx.fillStyle = '#f3f4f6';
            ctx.fill();
            ctx.strokeStyle = '#d1d5db';
            ctx.lineWidth = 3;
            ctx.stroke();

            ctx.fillStyle = '#9ca3af';
            ctx.font = 'bold 16px Tajawal';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('أضف مطاعم للعجلة', centerX, centerY);
            return;
        }

        const sliceAngle = (2 * Math.PI) / wheelItems.length;

        wheelItems.forEach((item, index) => {
            const startAngle = rotation + index * sliceAngle;
            const endAngle = startAngle + sliceAngle;

            // Draw slice
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, startAngle, endAngle);
            ctx.closePath();
            ctx.fillStyle = colors[index % colors.length];
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 3;
            ctx.stroke();

            // Draw text
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(startAngle + sliceAngle / 2);
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#fff';
            ctx.font = 'bold 14px Tajawal';
            ctx.fillText(item, radius * 0.65, 0);
            ctx.restore();
        });

        // Draw center circle
        ctx.beginPath();
        ctx.arc(centerX, centerY, 30, 0, 2 * Math.PI);
        ctx.fillStyle = '#fff';
        ctx.fill();
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = 3;
        ctx.stroke();
    }

    function addToWheel() {
        const input = document.getElementById('restaurant-input');
        const item = input.value.trim();

        if (!item) {
            alert('اكتب اسم مطعم!');
            return;
        }

        if (wheelItems.length >= 10) {
            alert('الحد الأقصى 10 مطاعم!');
            canvas.classList.add('shake');
            setTimeout(() => canvas.classList.remove('shake'), 300);
            return;
        }

        wheelItems.push(item);
        input.value = '';
        updateItemsList();
        drawWheel();
    }

    function handleInputKeypress(event) {
        if (event.key === 'Enter') {
            addToWheel();
        }
    }

    function removeItem(index) {
        wheelItems.splice(index, 1);
        updateItemsList();
        drawWheel();
    }

    function updateItemsList() {
        const list = document.getElementById('items-list');
        const count = document.getElementById('items-count');
        count.textContent = wheelItems.length;

        if (wheelItems.length === 0) {
            list.innerHTML = '<li class="text-gray-400 text-sm">لا توجد مطاعم بعد</li>';
            return;
        }

        list.innerHTML = wheelItems.map((item, index) => `
            <li class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded-lg">
                <span class="font-medium text-gray-700">${item}</span>
                <button onclick="removeItem(${index})" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </li>
        `).join('');
    }

    function clearWheel() {
        if (wheelItems.length === 0) return;
        if (confirm('هل تريد مسح كل المطاعم؟')) {
            wheelItems = [];
            updateItemsList();
            drawWheel();
            document.getElementById('winner-display').classList.add('hidden');
        }
    }

    function spinWheel() {
        if (isSpinning) return;
        if (wheelItems.length < 2) {
            alert('أضف على الأقل مطعمين!');
            return;
        }

        isSpinning = true;
        document.getElementById('spin-btn').disabled = true;
        document.getElementById('winner-display').classList.add('hidden');

        const spins = 5 + Math.random() * 5; // 5-10 rotations
        const targetRotation = rotation + spins * 2 * Math.PI + Math.random() * 2 * Math.PI;
        const duration = 3000; // 3 seconds
        const startTime = Date.now();
        const startRotation = rotation;

        function animate() {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Ease out cubic
            const easeProgress = 1 - Math.pow(1 - progress, 3);

            rotation = startRotation + (targetRotation - startRotation) * easeProgress;
            drawWheel();

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                isSpinning = false;
                document.getElementById('spin-btn').disabled = false;
                showWinner();
            }
        }

        animate();
    }

    function showWinner() {
        const sliceAngle = (2 * Math.PI) / wheelItems.length;
        // Pointer is at top (270 degrees or 3π/2 in our coordinate system)
        const pointerAngle = (Math.PI / 2) - rotation;
        const normalizedAngle = ((pointerAngle % (2 * Math.PI)) + 2 * Math.PI) % (2 * Math.PI);
        const winnerIndex = Math.floor(normalizedAngle / sliceAngle);
        const winner = wheelItems[winnerIndex];

        document.getElementById('winner-text').textContent = winner;
        document.getElementById('winner-display').classList.remove('hidden');
    }

    // Initialize
    drawWheel();
    updateItemsList();
</script>
@endpush
