@extends('layouts.app')

@section('title', ($currentLocale ?? 'ar') === 'ar' ? 'اتصل بنا | ناكل ايه' : 'Contact Us | Nakol Eh')
@section('meta_description', ($currentLocale ?? 'ar') === 'ar' ? 'طرق التواصل مع فريق موقع ناكل ايه بخصوص المحتوى أو الاقتراحات أو أي استفسار.' : 'Ways to contact the Nakol Eh team for content questions, suggestions, or inquiries.')
@section('meta_keywords', ($currentLocale ?? 'ar') === 'ar' ? 'اتصل بنا, تواصل, ناكل ايه' : 'contact us, support, nakol eh')

@section('content')
<section class="max-w-4xl mx-auto px-4 py-10 sm:py-14">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-4">
            {{ ($currentLocale ?? 'ar') === 'ar' ? 'اتصل بنا' : 'Contact Us' }}
        </h1>

        @php
            $contactEmail = 'momenshalapy262@gmail.com';
        @endphp

        @if(($currentLocale ?? 'ar') === 'ar')
            <div class="space-y-4 text-gray-600 leading-relaxed">
                <p>
                    لأي استفسار، اقتراح، أو طلب تحديث بيانات مطعم، يمكنك التواصل معنا عبر البريد الإلكتروني التالي:
                </p>
                <p class="text-lg font-semibold text-primary-600 break-all">
                    <a href="mailto:{{ $contactEmail }}" class="hover:text-primary-700 transition-colors">{{ $contactEmail }}</a>
                </p>
                <p>
                    نسعى للرد على الرسائل في أقرب وقت ممكن.
                </p>
            </div>
        @else
            <div class="space-y-4 text-gray-600 leading-relaxed">
                <p>
                    For any inquiry, suggestion, or restaurant data update request, please contact us via email:
                </p>
                <p class="text-lg font-semibold text-primary-600 break-all">
                    <a href="mailto:{{ $contactEmail }}" class="hover:text-primary-700 transition-colors">{{ $contactEmail }}</a>
                </p>
                <p>
                    We aim to respond as quickly as possible.
                </p>
            </div>
        @endif
    </div>
</section>
@endsection
