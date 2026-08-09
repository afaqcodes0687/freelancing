<style>
    /* --- aapki CSS same rakhi hai --- */
    .subcategory-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .subcategory-card {
        position: relative;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .subcategory-card input[type="checkbox"] {
        display: none;
    }

    .subcategory-card label {
        display: block;
        padding: 12px 15px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 10px;
    }

    .subcategory-card:hover {
        border-color: #309400;
        background: #f9fff6;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .subcategory-card input[type="checkbox"]:checked+label {
        border: 2px solid #309400;
        background: #f0fff0;
        color: #309400;
        font-weight: 600;
    }

    .subcategory-chip {
        display: inline-flex;
        align-items: center;
        background: #309400;
        color: white;
        padding: 5px 10px;
        margin: 3px;
        border-radius: 15px;
        font-size: 0.9rem;
        gap: 6px;
    }

    .subcategory-chip .remove-sub {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        font-size: 14px;
        color: #fff;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .show-more-category {
        font-size: 0.9rem;
        color: #309400;
        cursor: pointer;
        text-decoration: underline;
        border: none;
        background: none;
        margin-top: 10px;
        display: block;
    }

    .show-more-category:hover {
        text-decoration: none;
        color: #309400;
    }

    .accordion-button:not(.collapsed) {
        color: #309400;
    }

    .loading {
        color: #309400;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .loading i,
    #loadMoreCategories i {
        color: #309400;
        font-size: 16px;
    }

    .accordion-item {
        border: none !important;
    }

    .subcategory-card label {
        font-size: 15px;
        font-weight: 500;
    }

    .category-separator {
        border: 0;
        border-bottom: 1px solid #309400;
        margin: 8px 0;
        height: 0px !important;
    }

    .accordion-button {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 1.2rem 1.5rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c2c2c;
        text-align: left;
        background-color: #fff;
        border: none;
        border-radius: 8px;
        overflow-anchor: none;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .accordion-button:hover {
        background-color: #f9f9f9;
        color: #309400;
    }

    .accordion-button:not(.collapsed) {
        background-color: #f0fff5;
        color: #309400;
        font-weight: 700;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.08);
    }

    .accordion-button img,
    .accordion-button svg {
        margin-right: 10px;
        max-height: 28px;
    }

    .accordion-button.selected-category {
        background-color: #f0fff5 !important;
        border-left: 4px solid #309400;
        font-weight: 700;
        color: #309400;
    }
</style>

<div class="setup-wrapper-contents">
    <div class="setup-wrapper-contents-item">
        <h3 class="setup-wrapper-contents-title">
            {{ get_static_option('work_title') ?? __('What kinds of services will you provide to clients?(Work)') }}
        </h3>

        <!-- ✅ Chips section -->
        <div id="selected-subcategories" class="mt-3">
            @foreach($categories as $cat)
                @foreach(($cat->sub_categories ?? []) as $sub)
                    @if(in_array($sub->id, $userSubcategories ?? []))
                        <span class="subcategory-chip" data-cat="{{ $cat->id }}" data-id="{{ $sub->id }}">
                            {{ $sub->sub_category }} <span class="remove-sub">&times;</span>
                        </span>
                    @endif
                @endforeach
            @endforeach
        </div>

        <div class="setup-wrapper-work">
            <div class="row g-4">
                <input type="hidden" id="set_category_id"
                    value="{{ !empty($user_work) ? $user_work->category_id : '' }}">
                <input type="hidden" id="set_sub_category_id"
                    value="{{ !empty($user_work) ? $user_work->sub_category_id : '' }}">


                <div class="accordion" id="categoryAccordion">
                    @foreach($categories as $index => $cat)
                        @php
                            $catSelected = collect($userSubcategories ?? [])
                                ->intersect($cat->sub_categories->pluck('id'))
                                ->isNotEmpty();
                        @endphp
                        <div class="accordion-item mt-2 category-item {{ $index >= 6 ? 'd-none extra-category' : '' }}">
                            <h2 class="accordion-header" id="heading{{ $cat->id }}">
                                <button class="accordion-button collapsed {{ $catSelected ? 'selected-category' : '' }}"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $cat->id }}"
                                    aria-expanded="false" aria-controls="collapse{{ $cat->id }}">
                                    {!! render_image_markup_by_attachment_id($cat->image) !!}
                                    {{ $cat->category }}
                                </button>
                            </h2>
                            <div id="collapse{{ $cat->id }}" class="accordion-collapse collapse"
                                aria-labelledby="heading{{ $cat->id }}" data-bs-parent="#categoryAccordion">
                                <div class="accordion-body">
                                    <div class="subcategory-grid" id="subcategory-{{ $cat->id }}">
                                        @foreach($cat->sub_categories as $sub)
                                            <div class="subcategory-card">
                                                <input type="checkbox" class="subcategory-checkbox" data-cat="{{ $cat->id }}"
                                                    data-name="{{ $sub->sub_category }}" id="subcat-{{ $sub->id }}"
                                                    value="{{ $sub->id }}" {{ in_array($sub->id, $userSubcategories ?? []) ? 'checked' : '' }}>
                                                <label for="subcat-{{ $sub->id }}">{{ $sub->sub_category }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="category-separator {{ $index >= 6 ? 'd-none extra-category' : '' }}">
                    @endforeach
                </div>

                <!-- ✅ Hidden input (JSON for backend) -->
                <input type="hidden" name="subcategories" id="subcategory_input"
                    value='@json($userSubcategories ?? [])'>

                <!-- Load More Categories button -->
                @if(count($categories) > 6)
                    <button id="loadMoreCategories" class="show-more-category">Show More Category</button>
                @endif
            </div>
        </div>
    </div>
</div>
