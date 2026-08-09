@props(['category', 'subcategory' => null, 'childcategory' => null])

<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-inner">
                    <ul class="page-list">
                        <li class="list-item"><a href="{{ route('homepage') }}">{{ __('Home') }}</a></li>
                        <li class="list-item">
                            @if($subcategory)
                                <a href="{{ route('category.projects', $category->slug ?? '') }}">{{ $category->category ?? '' }}</a>
                            @else
                                <span>{{ $category->category ?? '' }}</span>
                            @endif
                        </li>
                        @if($subcategory)
                            <li class="list-item">
                                @if($childcategory)
                                    <a href="{{ route('subcategory.landing', ['category_slug' => $category->slug ?? '', 'sub_slug' => $subcategory->slug ?? '']) }}">{{ $subcategory->sub_category ?? '' }}</a>
                                @else
                                    <span>{{ $subcategory->sub_category ?? '' }}</span>
                                @endif
                            </li>
                        @endif
                        @if($childcategory)
                            <li class="list-item">
                                <span>{{ $childcategory->name ?? '' }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('head_scripts')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ route('homepage') }}"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "{{ $category->category ?? '' }}",
      "item": "{{ route('category.projects', $category->slug ?? '') }}"
    }
    @if($subcategory)
    ,{
      "@type": "ListItem",
      "position": 3,
      "name": "{{ $subcategory->sub_category ?? '' }}",
      "item": "{{ route('subcategory.landing', ['category_slug' => $category->slug ?? '', 'sub_slug' => $subcategory->slug ?? '']) }}"
    }
    @endif
    @if($childcategory)
    ,{
      "@type": "ListItem",
      "position": 4,
      "name": "{{ $childcategory->name ?? '' }}",
      "item": "{{ route('child_category.landing', ['category_slug' => $category->slug ?? '', 'sub_slug' => $subcategory->slug ?? '', 'child_slug' => $childcategory->slug ?? '']) }}"
    }
    @endif
  ]
}
</script>
@endpush
