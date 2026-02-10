{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

    {{-- Homepage --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
        <xhtml:link rel="alternate" hreflang="ar" href="{{ url('/') }}?lang=ar"/>
        <xhtml:link rel="alternate" hreflang="en" href="{{ url('/') }}?lang=en"/>
    </url>

    {{-- Search / Browse Page --}}
    <url>
        <loc>{{ url('/search') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- Nakol Eh Page --}}
    <url>
        <loc>{{ url('/nakl-eih') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

    {{-- Picker Wheel Page --}}
    <url>
        <loc>{{ url('/picker-wheel') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    {{-- Restaurant Pages --}}
    @foreach($restaurants as $restaurant)
    <url>
        <loc>{{ url('/restaurant/' . $restaurant->slug) }}</loc>
        <lastmod>{{ $restaurant->updated_at->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>{{ $restaurant->total_views > 100 ? '0.9' : ($restaurant->total_views > 10 ? '0.8' : '0.7') }}</priority>
    </url>
    @endforeach

    {{-- City-filtered search pages --}}
    @foreach($cities as $city)
    <url>
        <loc>{{ url('/search?city_id=' . $city->id) }}</loc>
        <lastmod>{{ $city->updated_at->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    {{-- Category-filtered search pages --}}
    @foreach($categories as $category)
    <url>
        <loc>{{ url('/search?category_id=' . $category->id) }}</loc>
        <lastmod>{{ $category->updated_at->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

</urlset>
