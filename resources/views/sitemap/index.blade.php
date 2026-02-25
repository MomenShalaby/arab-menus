{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

    {{-- Homepage (Arabic - default) --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ optional($restaurants->first())->updated_at?->toDateString() ?? now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
        <xhtml:link rel="alternate" hreflang="ar"        href="{{ url('/') }}"/>
        <xhtml:link rel="alternate" hreflang="en"        href="{{ url('/') }}?lang=en"/>
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ url('/') }}"/>
    </url>

    {{-- Homepage (English) --}}
    <url>
        <loc>{{ url('/') }}?lang=en</loc>
        <lastmod>{{ optional($restaurants->first())->updated_at?->toDateString() ?? now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
        <xhtml:link rel="alternate" hreflang="ar"        href="{{ url('/') }}"/>
        <xhtml:link rel="alternate" hreflang="en"        href="{{ url('/') }}?lang=en"/>
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ url('/') }}"/>
    </url>

    {{-- Search Page --}}
    <url>
        <loc>{{ url('/search') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
        <xhtml:link rel="alternate" hreflang="ar"        href="{{ url('/search') }}"/>
        <xhtml:link rel="alternate" hreflang="en"        href="{{ url('/search') }}?lang=en"/>
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ url('/search') }}"/>
    </url>

    {{-- Nakol Eh Page --}}
    <url>
        <loc>{{ url('/nakl-eih') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
        <xhtml:link rel="alternate" hreflang="ar"        href="{{ url('/nakl-eih') }}"/>
        <xhtml:link rel="alternate" hreflang="en"        href="{{ url('/nakl-eih') }}?lang=en"/>
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ url('/nakl-eih') }}"/>
    </url>

    {{-- Picker Wheel --}}
    <url>
        <loc>{{ url('/picker-wheel') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
        <xhtml:link rel="alternate" hreflang="ar"        href="{{ url('/picker-wheel') }}"/>
        <xhtml:link rel="alternate" hreflang="en"        href="{{ url('/picker-wheel') }}?lang=en"/>
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ url('/picker-wheel') }}"/>
    </url>

    {{-- About Us --}}
    <url>
        <loc>{{ url('/about-us') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
        <xhtml:link rel="alternate" hreflang="ar"        href="{{ url('/about-us') }}"/>
        <xhtml:link rel="alternate" hreflang="en"        href="{{ url('/about-us') }}?lang=en"/>
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ url('/about-us') }}"/>
    </url>

    {{-- Contact Us --}}
    <url>
        <loc>{{ url('/contact-us') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
        <xhtml:link rel="alternate" hreflang="ar"        href="{{ url('/contact-us') }}"/>
        <xhtml:link rel="alternate" hreflang="en"        href="{{ url('/contact-us') }}?lang=en"/>
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ url('/contact-us') }}"/>
    </url>

    {{-- Privacy Policy --}}
    <url>
        <loc>{{ url('/privacy-policy') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
        <xhtml:link rel="alternate" hreflang="ar"        href="{{ url('/privacy-policy') }}"/>
        <xhtml:link rel="alternate" hreflang="en"        href="{{ url('/privacy-policy') }}?lang=en"/>
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ url('/privacy-policy') }}"/>
    </url>

    {{-- Restaurant Pages (both language versions) --}}
    @foreach ($restaurants as $restaurant)
        @php
            $arLoc = url('/restaurant/' . $restaurant->slug);
            $enLoc = $arLoc . '?lang=en';
        @endphp

        {{-- Arabic (canonical / default) --}}
        <url>
            <loc>{{ $arLoc }}</loc>
            <lastmod>{{ $restaurant->updated_at?->toDateString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>{{ $restaurant->total_views > 100 ? '0.9' : ($restaurant->total_views > 10 ? '0.8' : '0.7') }}</priority>
            <xhtml:link rel="alternate" hreflang="ar"        href="{{ $arLoc }}"/>
            <xhtml:link rel="alternate" hreflang="en"        href="{{ $enLoc }}"/>
            <xhtml:link rel="alternate" hreflang="x-default" href="{{ $arLoc }}"/>
            @if (!empty($restaurant->image))
                <image:image>
                    <image:loc>{{ $restaurant->image }}</image:loc>
                    <image:title>{{ $restaurant->name ?? $restaurant->slug }}</image:title>
                </image:image>
            @endif
        </url>

        {{-- English version --}}
        <url>
            <loc>{{ $enLoc }}</loc>
            <lastmod>{{ $restaurant->updated_at?->toDateString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>{{ $restaurant->total_views > 100 ? '0.8' : ($restaurant->total_views > 10 ? '0.7' : '0.6') }}</priority>
            <xhtml:link rel="alternate" hreflang="ar"        href="{{ $arLoc }}"/>
            <xhtml:link rel="alternate" hreflang="en"        href="{{ $enLoc }}"/>
            <xhtml:link rel="alternate" hreflang="x-default" href="{{ $arLoc }}"/>
        </url>
    @endforeach

</urlset>
