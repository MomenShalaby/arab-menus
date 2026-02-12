@extends('layouts.app')

@section('title', ($currentLocale ?? 'ar') === 'ar' ? 'سياسة الخصوصية | ناكل ايه' : 'Privacy Policy | Nakol Eh')
@section('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'سياسة الخصوصية الخاصة بموقع ناكل ايه وطريقة جمع واستخدام البيانات وملفات تعريف الارتباط.' : 'Nakol Eh privacy policy and how we collect, use, and protect data and cookies.')
@section('meta_keywords', ($currentLocale ?? 'ar') === 'ar' ? 'سياسة الخصوصية, ملفات تعريف الارتباط, ناكل ايه' : 'privacy policy, cookies, nakol eh')

@section('content')
<section class="max-w-4xl mx-auto px-4 py-10 sm:py-14">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-6">
            {{ ($currentLocale ?? 'ar') === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}
        </h1>

        @if(($currentLocale ?? 'ar') === 'ar')
            <div class="space-y-5 text-gray-600 leading-relaxed">
                <p>
                    نحن في <strong class="text-gray-800">ناكل ايه</strong> نحترم خصوصيتك. توضّح هذه الصفحة كيفية استخدام المعلومات عند تصفح الموقع.
                </p>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-2">1) المعلومات التي نجمعها</h2>
                    <p>قد نجمع بيانات استخدام عامة مثل الصفحات الأكثر زيارة ونوع الجهاز والمتصفح لتحسين الأداء وتجربة المستخدم.</p>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-2">2) ملفات تعريف الارتباط (Cookies)</h2>
                    <p>نستخدم ملفات تعريف الارتباط لتحسين تجربة الاستخدام ودعم عرض الإعلانات. يمكنك التحكم بها من إعدادات المتصفح.</p>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-2">3) المحتوى ومصادر الصور</h2>
                    <p>قد يتم عرض صور منيو ومعلومات مطاعم من مصادر متاحة على الويب. إذا كنت صاحب حقوق محتوى وتحتاج تعديلًا أو إزالة، تواصل معنا وسيتم التعامل سريعًا.</p>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-2">4) التواصل</h2>
                    <p>
                        لأي استفسار بخصوص الخصوصية أو المحتوى:
                        <a href="mailto:momenshalapy262@gmail.com" class="text-primary-600 hover:text-primary-700 font-semibold">momenshalapy262@gmail.com</a>
                    </p>
                </div>
            </div>
        @else
            <div class="space-y-5 text-gray-600 leading-relaxed">
                <p>
                    At <strong class="text-gray-800">Nakol Eh</strong>, we respect your privacy. This page explains how information is used when you browse the website.
                </p>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-2">1) Information We Collect</h2>
                    <p>We may collect general usage data such as popular pages, device type, and browser type to improve performance and user experience.</p>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-2">2) Cookies</h2>
                    <p>We use cookies to improve browsing experience and support ads. You can manage cookies from your browser settings.</p>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-2">3) Content & Image Sources</h2>
                    <p>Menu images and restaurant information may be collected from publicly available web sources. If you are a rights holder and need correction or removal, contact us and we will handle it promptly.</p>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-2">4) Contact</h2>
                    <p>
                        For privacy or content inquiries:
                        <a href="mailto:momenshalapy262@gmail.com" class="text-primary-600 hover:text-primary-700 font-semibold">momenshalapy262@gmail.com</a>
                    </p>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
