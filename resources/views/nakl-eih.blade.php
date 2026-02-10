@extends('layouts.app')

@section('title', ($currentLocale ?? 'ar') === 'ar' ? 'ناكل ايه؟ - اختر مطعمك عشوائياً | دليل المطاعم في مصر' : 'Nakol Eh? - Random Restaurant Picker | Egypt Restaurant Guide')
@section('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'مش عارف تاكل ايه؟ خلينا نساعدك! اختار من الفئات اللي بتحبها وهنختارلك مطعم عشوائي من أكتر من ' . (\App\Models\Restaurant::count()) . ' مطعم' : 'Can\'t decide what to eat? Let us help! Pick your favorite categories and we\'ll choose a random restaurant for you')
@section('meta_keywords', 'ناكل ايه, اختيار مطعم عشوائي, random restaurant picker, مطاعم مصر, اقتراح مطعم')

@push('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "ناكل ايه؟ - اختيار مطعم عشوائي",
    "description": "اختر مطعمك عشوائياً من بين آلاف المطاعم في مصر",
    "url": "{{ route('nakl-eih') }}",
    "applicationCategory": "FoodService",
    "operatingSystem": "Web"
}
</script>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8 sm:py-12">
        <!-- Header -->
        <div class="text-center mb-8 sm:mb-12">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-800 mb-3 sm:mb-4">ناكل ايه؟ 🤔</h1>
            <p class="text-base sm:text-lg text-gray-600">مش عارف تاكل ايه؟ اختار الفئات اللي بتحبها وهنختارلك مطعم عشوائياً!</p>
        </div>

        <!-- Category Selection -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                اختار نوع الأكل
            </h2>

            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($categories as $category)
                    <button onclick="toggleCategory({{ $category->id }})"
                        data-category-id="{{ $category->id }}"
                        class="nakl-cat-btn px-4 py-2 rounded-full border-2 border-gray-200 text-gray-700 font-medium transition-all duration-200 hover:border-primary-400">
                        {{ $category->name_ar ?? $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- City Filter (Optional) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">اختار المدينة (اختياري)</label>
                <select id="nakl-city" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none">
                    <option value="">كل المدن</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name_ar ?? $city->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pick Button -->
            <button onclick="pickRandomRestaurant()"
                class="w-full bg-gradient-to-r from-primary-500 to-accent-500 text-white font-bold py-4 px-8 rounded-xl hover:from-primary-600 hover:to-accent-600 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    اختارلي مطعم!
                </span>
            </button>
        </div>

        <!-- Result -->
        <div id="nakl-result" class="hidden"></div>
    </div>

    <style>
        .nakl-cat-btn.active {
            background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
            border-color: #ef4444;
            color: white;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.3s ease;
        }
    </style>
@endsection

@push('scripts')
<script>
    let selectedCategories = [];

    function toggleCategory(categoryId) {
        const btn = document.querySelector(`[data-category-id="${categoryId}"]`);
        if (selectedCategories.includes(categoryId)) {
            selectedCategories = selectedCategories.filter(id => id !== categoryId);
            btn.classList.remove('active');
        } else {
            selectedCategories.push(categoryId);
            btn.classList.add('active');
        }
    }

    async function pickRandomRestaurant() {
        const cityId = document.getElementById('nakl-city').value;
        const resultDiv = document.getElementById('nakl-result');

        if (selectedCategories.length === 0 && !cityId) {
            alert('اختار على الأقل فئة واحدة أو مدينة!');
            return;
        }

        resultDiv.innerHTML = '<div class="text-center py-12"><div class="animate-spin w-12 h-12 border-4 border-primary-500 border-t-transparent rounded-full mx-auto"></div><p class="text-gray-600 mt-4">جاري البحث...</p></div>';
        resultDiv.classList.remove('hidden');

        try {
            const params = new URLSearchParams();
            if (selectedCategories.length > 0) {
                params.set('category_ids', selectedCategories.join(','));
            }
            if (cityId) {
                params.set('city_id', cityId);
            }

            const response = await fetch(`/api/random-restaurant?${params}`);
            const data = await response.json();

            if (data.error) {
                resultDiv.innerHTML = `
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center fade-in">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-bold text-gray-500 mb-2">للأسف مفيش مطاعم</h3>
                        <p class="text-gray-400 mb-4">${data.error}</p>
                        <button onclick="pickRandomRestaurant()" class="text-primary-600 hover:text-primary-700 font-semibold">جرب تاني</button>
                    </div>
                `;
            } else {
                const categories = data.categories.map(c => c.name_ar || c.name).join(' • ');
                resultDiv.innerHTML = `
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden fade-in">
                        <div class="bg-gradient-to-r from-primary-500 to-accent-500 text-white p-6 text-center">
                            <h2 class="text-2xl font-bold mb-2">🎉 اقتراحنا ليك</h2>
                            <p class="text-primary-100">ده المطعم المناسب!</p>
                        </div>
                        <div class="p-8">
                            <div class="flex flex-col sm:flex-row items-center gap-6">
                                ${data.logo_url ? `
                                    <img src="${data.logo_url}" alt="${data.name}" class="w-24 h-24 object-contain rounded-xl border border-gray-100">
                                ` : `
                                    <div class="w-24 h-24 bg-primary-100 rounded-xl flex items-center justify-center">
                                        <span class="text-4xl font-bold text-primary-600">${data.name.charAt(0)}</span>
                                    </div>
                                `}
                                <div class="flex-1 text-center sm:text-right">
                                    <h3 class="text-3xl font-extrabold text-gray-800 mb-2">${data.name}</h3>
                                    ${categories ? `<p class="text-gray-500 mb-3">${categories}</p>` : ''}
                                    ${data.hotline ? `
                                        <a href="tel:${data.hotline}" class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-semibold bg-green-50 px-4 py-2 rounded-full mb-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            ${data.hotline}
                                        </a>
                                    ` : ''}
                                </div>
                            </div>
                            <div class="flex gap-3 mt-6">
                                <a href="${data.url}" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-xl text-center transition-colors">
                                    شوف المنيو
                                </a>
                                <button onclick="pickRandomRestaurant()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-6 rounded-xl transition-colors">
                                    جرب تاني
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }
        } catch (error) {
            resultDiv.innerHTML = `
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center fade-in">
                    <p class="text-red-500">حصل خطأ. حاول تاني.</p>
                </div>
            `;
        }
    }
</script>
@endpush
