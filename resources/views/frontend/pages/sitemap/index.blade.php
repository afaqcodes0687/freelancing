<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

{{-- ══ Static pages ══ --}}
@foreach($staticUrls as $page)
<url>
    <loc>{{ $page['loc'] }}</loc>
    <lastmod>{{ $now }}</lastmod>
    <changefreq>{{ $page['changefreq'] }}</changefreq>
    <priority>{{ $page['priority'] }}</priority>
</url>
@endforeach

{{-- ══ Category hubs ══ --}}
@foreach($categories as $cat)
<url>
    <loc>{{ route('category.projects', $cat->slug) }}</loc>
    <lastmod>{{ optional($cat->updated_at)->toAtomString() ?? $now }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.85</priority>
</url>
@endforeach

{{-- ══ Subcategory landing pages ══ --}}
@foreach($subcategories as $sub)
    @if($sub->category)
<url>
    <loc>{{ route('subcategory.landing', ['category_slug' => $sub->category->slug, 'sub_slug' => $sub->slug]) }}</loc>
    <lastmod>{{ optional($sub->updated_at)->toAtomString() ?? $now }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.80</priority>
</url>
    @endif
@endforeach

{{-- ══ Child category landing pages ══ --}}
@foreach($childCategories as $child)
    @if($child->sub_category && $child->sub_category->category)
<url>
    <loc>{{ route('child_category.landing', [
        'category_slug' => $child->sub_category->category->slug,
        'sub_slug'      => $child->sub_category->slug,
        'child_slug'    => $child->slug,
    ]) }}</loc>
    <lastmod>{{ optional($child->updated_at)->toAtomString() ?? $now }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.70</priority>
</url>
    @endif
@endforeach

</urlset>
