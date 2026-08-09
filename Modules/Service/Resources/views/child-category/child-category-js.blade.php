<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            // ── Select2 init for category filters & subcategory dropdowns ──
            $('.add_category_filter_select2').select2({
                dropdownParent: $('#addModal')
            });
            $('.subcategory_select2').select2({
                dropdownParent: $('#addModal')
            });
            $('.edit_category_filter_select2').select2({
                dropdownParent: $('#editChildCategoryModal')
            });
            $('.subcategory_select22').select2({
                dropdownParent: $('#editChildCategoryModal')
            });

            // ── Helper: load subcategories by category into a given select ──
            function loadSubcategoriesByCategory(categoryId, $targetSelect, $infoSpan, preselectId) {
                var ajaxUrl = "{{ route('au.subcategory.all') }}";

                if (!categoryId) {
                    // No filter — restore the full subcategory list (generated inline by Blade)
                    var defaultOptions = "<option value=''>{{ __('Select Sub Category') }}</option>";
                    @foreach(\Modules\Service\Entities\SubCategory::all_sub_categories() as $sub)
                        defaultOptions += '<option value="{{ $sub->id }}">{{ $sub->sub_category }}</option>';
                    @endforeach
                    $targetSelect.html(defaultOptions);
                    if (preselectId) { $targetSelect.val(preselectId); }
                    $targetSelect.trigger('change.select2');
                    $infoSpan.hide();
                    return;
                }

                $infoSpan.show().html('<span style="color:#309400;"><i class="fas fa-spinner fa-spin"></i> Loading...</span>');

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', category: categoryId },
                    success: function(res) {
                        if (res.status === 'success') {
                            var options = "<option value=''>{{ __('Select Sub Category') }}</option>";
                            $.each(res.subcategories, function(i, sub) {
                                options += '<option value="' + sub.id + '">' + sub.sub_category + '</option>';
                            });
                            $targetSelect.html(options);
                            if (preselectId) { $targetSelect.val(preselectId); }
                            $targetSelect.trigger('change.select2');

                            if (res.subcategories.length === 0) {
                                $infoSpan.show().html('<span class="text-danger">{{ __("No subcategories found for this category!") }}</span>');
                            } else {
                                $infoSpan.hide();
                            }
                        } else {
                            $infoSpan.show().html('<span class="text-danger">{{ __("Failed to load subcategories") }}</span>');
                        }
                    },
                    error: function() {
                        $infoSpan.show().html('<span class="text-danger">{{ __("Something went wrong!") }}</span>');
                    }
                });
            }

            // ── ADD Modal: Category filter → load subcategories ──
            $(document).on('change', '#add_category_filter', function() {
                var categoryId = $(this).val();
                loadSubcategoriesByCategory(categoryId, $('#add_sub_category'), $('#add_subcategory_info'), null);
            });

            // ── EDIT Modal: Category filter → load subcategories ──
            $(document).on('change', '#edit_category_filter', function() {
                var categoryId = $(this).val();
                loadSubcategoriesByCategory(categoryId, $('#edit_sub_category'), $('#edit_subcategory_info'), null);
            });

            // slug generate
            function transliterateCyrillic(text) {
                const cyrillicToLatinMap = {
                    'А': 'A', 'а': 'a', 'Б': 'B', 'б': 'b', 'В': 'V', 'в': 'v',
                    'Г': 'G', 'г': 'g', 'Д': 'D', 'д': 'd', 'Е': 'E', 'е': 'e',
                    'Ё': 'Yo', 'ё': 'yo', 'Ж': 'Zh', 'ж': 'zh', 'З': 'Z', 'з': 'z',
                    'И': 'I', 'и': 'i', 'Й': 'Y', 'й': 'y', 'К': 'K', 'к': 'k',
                    'Л': 'L', 'л': 'l', 'М': 'M', 'м': 'm', 'Н': 'N', 'н': 'n',
                    'О': 'O', 'о': 'o', 'П': 'P', 'п': 'p', 'Р': 'R', 'р': 'r',
                    'С': 'S', 'с': 's', 'Т': 'T', 'т': 't', 'У': 'U', 'у': 'u',
                    'Ф': 'F', 'ф': 'f', 'Х': 'Kh', 'х': 'kh', 'Ц': 'Ts', 'ц': 'ts',
                    'Ч': 'Ch', 'ч': 'ch', 'Ш': 'Sh', 'ш': 'sh', 'Щ': 'Shch', 'щ': 'shch',
                    'Ъ': '', 'ъ': '', 'Ы': 'Y', 'ы': 'y', 'Ь': '', 'ь': '',
                    'Э': 'E', 'э': 'e', 'Ю': 'Yu', 'ю': 'yu', 'Я': 'Ya', 'я': 'ya',
                    // Additional characters for other Cyrillic-based languages
                    'Ә': 'Ae', 'ә': 'ae', 'Ғ': 'Gh', 'ғ': 'gh', 'Қ': 'Q', 'қ': 'q',
                    'Ң': 'Ng', 'ң': 'ng', 'Ө': 'Oe', 'ө': 'oe', 'Ұ': 'U', 'ұ': 'u',
                    'Ү': 'Ue', 'ү': 'ue', 'Һ': 'H', 'һ': 'h', 'І': 'I', 'і': 'i',
                    // Ukrainian specific
                    'Є': 'Ye', 'є': 'ye', 'І': 'I', 'і': 'i', 'Ї': 'Yi', 'ї': 'yi',
                    'Ґ': 'G', 'ґ': 'g',
                    // Belarusian specific
                    'Ў': 'U', 'ў': 'u',
                    // Serbian specific
                    'Ђ': 'Dj', 'ђ': 'dj', 'Ј': 'J', 'ј': 'j', 'Љ': 'Lj', 'љ': 'lj',
                    'Њ': 'Nj', 'њ': 'nj', 'Ћ': 'C', 'ћ': 'c', 'Џ': 'Dz', 'џ': 'dz',
                    // Macedonian specific
                    'Ѓ': 'Gj', 'ѓ': 'gj', 'Ѕ': 'Dz', 'ѕ': 'dz', 'Ќ': 'Kj', 'ќ': 'kj',
                    'Љ': 'Lj', 'љ': 'lj', 'Њ': 'Nj', 'њ': 'nj', 'Џ': 'Dz', 'џ': 'dz'
                };

                const arabicToLatinMap = {
                    'ا': 'a', 'أ': 'a', 'إ': 'i', 'آ': 'aa', 'ب': 'b', 'ت': 't', 'ث': 'th',
                    'ج': 'j', 'ح': 'h', 'خ': 'kh', 'د': 'd', 'ذ': 'dh', 'ر': 'r', 'ز': 'z',
                    'س': 's', 'ش': 'sh', 'ص': 's', 'ض': 'd', 'ط': 't', 'ظ': 'dh', 'ع': 'a',
                    'غ': 'gh', 'ف': 'f', 'ق': 'q', 'ك': 'k', 'ل': 'l', 'م': 'm', 'ن': 'n',
                    'ه': 'h', 'و': 'w', 'ي': 'y', 'ى': 'a', 'ة': 'h', 'ئ': 'e', 'ء': 'a',
                    'ؤ': 'o', 'لا': 'la'
                };

                return text.split('').map(char => {
                    return cyrillicToLatinMap[char] || arabicToLatinMap[char] || char;
                }).join('');
            }

            function generateSlug(text) {
                return transliterateCyrillic(text)
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            $('#name').on('input', function() {
                var slug = generateSlug($(this).val());
                $('#slug').val(slug);
            });

            $('#edit_name').on('input', function() {
                var slug = generateSlug($(this).val());
                $('#edit_slug').val(slug);
            });

            // edit modal data populate
            $(document).on('click', '.edit_child_category_modal', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var img_id = $(this).data('img_id');
                var img_url = $(this).data('img_url');
                var name = $(this).data('name');
                var meta_title = $(this).data('meta_title');
                var meta_description = $(this).data('meta_description');
                var short_description = $(this).data('short_description');
                var slug = $(this).data('slug');
                var sub_category = $(this).data('sub_category');

                $('#edit_child_category_id').val(id);
                $('#edit_name').val(name);
                $('#edit_slug').val(slug);
                $('#edit_meta_title').val(meta_title);
                $('#edit_meta_description').val(meta_description);
                $('#edit_short_description').val(short_description);

                // Pre-select the subcategory (select2 must be triggered after options are loaded)
                // First reset category filter, then load all and pre-select
                $('#edit_category_filter').val('').trigger('change.select2');
                loadSubcategoriesByCategory('', $('#edit_sub_category'), $('#edit_subcategory_info'), sub_category);

                // image preview
                if(img_id){
                    $('.image-preview-area').removeClass('d-none');
                    $('.image-preview-area img').attr('src',img_url);
                    $('.delete_image_btn').removeClass('d-none');
                    $('.delete_image_btn').attr('data-id',img_id);
                }else{
                    $('.image-preview-area').addClass('d-none');
                    $('.delete_image_btn').addClass('d-none');
                }
            });

            // add child category form submit
            $('.add_child_category').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var formData = new FormData(form[0]);
                var url = form.attr('action');
                var method = form.attr('method');

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.status === 'error'){
                            $('.error-message').remove();
                            $.each(response.errors, function(key, value) {
                                $('[name="'+key+'"]').closest('.form-group').append('<div class="error-message text-danger mt-2">'+value+'</div>');
                            });
                        } else {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $('.error-message').remove();
                        $.each(errors, function(key, value) {
                            $('[name="'+key+'"]').closest('.form-group').append('<div class="error-message text-danger mt-2">'+value+'</div>');
                        });
                    }
                });
            });

            // update child category form submit
            $('.update_child_category').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var formData = new FormData(form[0]);
                var url = form.attr('action');
                var method = form.attr('method');

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.status === 'error'){
                            $('.error-message').remove();
                            $.each(response.errors, function(key, value) {
                                $('[name="'+key+'"]').closest('.form-group').append('<div class="error-message text-danger mt-2">'+value+'</div>');
                            });
                        } else {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $('.error-message').remove();
                        $.each(errors, function(key, value) {
                            $('[name="'+key+'"]').closest('.form-group').append('<div class="error-message text-danger mt-2">'+value+'</div>');
                        });
                    }
                });
            });
        });
    })(jQuery);
</script>
