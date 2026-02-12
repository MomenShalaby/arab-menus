@extends('layouts.app')

@section('title', ($currentLocale ?? 'ar') === 'ar' ? 'من نحن | ناكل ايه' : 'About Us | Nakol Eh')
@section('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'تعرف على موقع ناكل ايه ورسالتنا في تقديم دليل واضح ومحدث لمنيوهات المطاعم في مصر.' : 'Learn about Nakol Eh and our mission to provide a clear and updated guide for restaurant menus in Egypt.')
@section('meta_keywords', ($currentLocale ?? 'ar') === 'ar' ? 'من نحن, ناكل ايه, دليل منيوهات المطاعم' : 'about us, nakol eh, restaurant menus guide')

@section('content')
<section class="max-w-4xl mx-auto px-4 py-10 sm:py-14">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-4">
            {{ ($currentLocale ?? 'ar') === 'ar' ? 'من نحن' : 'About Us' }}
        </h1>

        @if(($currentLocale ?? 'ar') === 'ar')
            <div class="space-y-4 text-gray-600 leading-relaxed">
                <p>
                    <strong class="text-gray-800">ناكل ايه</strong> هو دليل إلكتروني يساعدك في الوصول إلى منيوهات المطاعم في مصر بشكل سريع وسهل.
                    هدفنا هو توفير تجربة بحث واضحة للمستخدمين مع معلومات محدثة عن المطاعم مثل الأسعار، الفروع، وصور المنيو.
                </p>
                <p>
                    نعمل على تحسين المحتوى بشكل مستمر، وإضافة مطاعم جديدة وتحديث البيانات الحالية حتى يجد الزائر المعلومات التي يحتاجها بسهولة.
                </p>
                <p>
                    نلتزم بعرض المحتوى بشكل منظم، وتقديم تجربة استخدام جيدة على الهاتف والكمبيوتر، مع احترام سياسات المحتوى والجودة.
                </p>
            </div>
        @else
            <div class="space-y-4 text-gray-600 leading-relaxed">
                <p>
                    <strong class="text-gray-800">Nakol Eh</strong> is an online directory that helps users quickly find restaurant menus in Egypt.
                    Our mission is to provide a clear search experience with updated restaurant information such as prices, branches, and menu images.
                </p>
                <p>
                    We continuously improve the website, add new restaurants, and keep existing data up to date so visitors can find what they need easily.
                </p>
                <p>
                    We are committed to organized, quality content and a good browsing experience on both mobile and desktop while respecting content and quality policies.
                </p>
            </div>
        @endif
    </div>
</section>
@endsection
