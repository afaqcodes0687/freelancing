<script>
    $(document).ready(function (){
        setTimeout(function (){
            $("#offer_packages_available_or_not").trigger('change');
        },1000)
    });

    (function ($) {
        "use strict";

        let validation = {
            project_title_error: false
        };

        pre_next();

        $(document).ready(function () {

            $('.category_select2').select2();
            $('.subcategory_select2').select2();
            let check_package_titles = {
                "status":false
            };

            var url = `{{url('/')}}/{{ $project_details->slug }}`;
            $('.display_project_slug').text(url);

            // change country and get state
            $('#subcategory_info').hide();
            $(document).on('change', '#category', function() {
            let category = $(this).val();
            $('#subcategory_info').show().html('<span style="color:#309400;">Loading subcategories...</span>');
            
            $.ajax({
                method: 'post',
                url: "{{ route('au.subcategory.all') }}",
                data: {
                    category: category
                },
                success: function(res) {
                    if (res.status === 'success') {
                        let all_options = "<option value=''>{{ __('Select Sub Category') }}</option>";
                        let all_subcategories = res.subcategories;

                        $.each(all_subcategories, function(index, value) {
                            all_options += `<option value="${value.id}">${value.sub_category}</option>`;
                        });

                        $(".get_subcategory").html(all_options);
                        $("#subcategory_info").html('');
                        
                        // clear child category wrapper
                        $('#child_category_wrapper').hide();
                        $('#child_category_info').hide();

                        if (all_subcategories.length <= 0) {
                            $("#subcategory_info").html('<span class="text-danger">{{ __("No sub categories found for selected category!") }}</span>');
                        }
                    } else {
                        $("#subcategory_info").html('<span class="text-danger">{{ __("Failed to load sub categories!") }}</span>');
                    }
                },
                error: function() {
                    $("#subcategory_info").html('<span class="text-danger">{{ __("Error loading sub categories!") }}</span>');
                }
            });
        });

            // change subcategory and get child category
            $('#child_category_info').hide();
            $('.child_category_select2').select2();
            $(document).on('change', '#subcategory', function() {
                let selected_subcategories = $(this).val();
                
                if (selected_subcategories && selected_subcategories.length > 0) {
                    $('#child_category_wrapper').show();
                    
                    $('#child_category_info')
                        .show()
                        .html('<span style="color:#309400;"><i class="fas fa-spinner fa-spin"></i> Loading child categories...</span>');

                    // Fetch child categories for the first selected subcategory
                    $.ajax({
                        method: 'post',
                        url: "{{ route('au.childcategory.all') }}",
                        data: { subcategory: selected_subcategories[0] },
                        success: function(res) {
                            if (res.status === 'success') {
                                let all_child_categories = res.child_categories;
                                let all_options = "<option value=''>{{ __('Select Child Category') }}</option>";

                                $.each(all_child_categories, function(index, value) {
                                    all_options += `<option value="${value.id}">${value.name}</option>`;
                                });

                                $("#child_category").html(all_options);
                                $('.child_category_select2').trigger('change.select2');

                                if (all_child_categories.length <= 0) {
                                    $('#child_category_info').html('<span class="text-danger">{{ __('No child categories found for selected subcategory!') }}</span>');
                                    $('#child_category_wrapper').hide();
                                } else {
                                    $('#child_category_info').hide();
                                }
                            } else {
                                $('#child_category_info').html('<span class="text-danger">{{ __('Failed to load child categories') }}</span>');
                                $('#child_category_wrapper').hide();
                            }
                        },
                        error: function() {
                            $('#child_category_info').html('<span class="text-danger">{{ __('Something went wrong! Please try again.') }}</span>');
                            $('#child_category_wrapper').hide();
                        }
                    });
                } else {
                    $('#child_category_wrapper').hide();
                    $('#child_category_info').hide();
                }
            });


            // project title length check
            $('#project_title_char_length_check').hide();
            $('#project_title').on('keydown keyup change', function(){
                $('#project_title_char_length_check').show();
                let title_min_length = 5;
                let title_max_length = 100;
                let project_title_length = $('#project_title').val().length;

                if(project_title_length < title_min_length){
                    $('#project_title_char_length_check').html('<p class="text text-danger">{{ __('Length is short, minimum') }} '+ title_min_length +' {{ __('required') }}.</p>');
                }else if(project_title_length > title_max_length){
                    $('#project_title_char_length_check').html('<p class="text text-danger">{{ __('Length is not valid, maximum') }} '+ title_max_length +' {{ __('allowed') }}.</p>');
                }else{
                    $('#project_title_char_length_check').html('<p class="text text-success">{{ __('Length is valid') }}</p>');
                }
            });

            //slug generate
            // function makeSlug(slug){
            //     let finalSlug = slug.replace(/[^a-zA-Z0-9]/g, ' ');
            //     finalSlug = slug.replace(/  +/g, ' ');
            //     finalSlug = slug.replace(/\s/g, '-').toLowerCase().replace(/[^\w-]+/g, '-');
            //     return finalSlug;
            // }

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

                const langToLatinMap = currentLang() === 'ar' ? arabicToLatinMap : cyrillicToLatinMap;

                return text.split('').map(char => langToLatinMap[char] || char).join('');
            }

            function convertToSlug(text) {
                const transliteratedText = transliterateCyrillic(text);

                return transliteratedText
                    .toLowerCase()
                    .trim()
                    .replace(/\s+/g, '-');           // Replace spaces with -
            }

            function currentLang()
            {
                return document.documentElement.lang === 'ar' ? 'ar' : 'cy';
            }

            $(document).on('keyup', '#slug', function (e) {
                $('.display_project_slug').text('');
                let slug = convertToSlug($(this).val());
                $('#slug').val(slug);

                let url = `{{url('/')}}/` + slug;
                $('.full-slug-show').text(url);
            });

            //update slug
            $(document).on('click','.edit_project_slug',function(){
                $('.display_label_title').removeClass('d-none');
                $('#slug').removeClass('d-none');
            })

            // check package is available or not
            $(document).on('change','#offer_packages_available_or_not',function (e) {
                if($(this).prop('checked')){
                    $('.disabled_or_not'). prop('disabled', false);
                    $('#offer_packages_available_or_not').val('1')
                }else{
                    $('.disabled_or_not'). prop('disabled', true);
                    $('#offer_packages_available_or_not').val('0')
                }

            });

            // todo select checkbox or numeric
            $('.package-field-input .disabled_or_not').remove();
            // $(document).on('keyup','.checkbox_or_numeric_title',function(){
            //     let variable_name = $(this).val().toLowerCase().replaceAll(" ","_");
            //     let currentRow = $(this).closest(".append-include").find("td input");
            //
            //     let t_array = [
            //         "basic",
            //         "standard",
            //         "premium"
            //     ];
            //     let i = 0;
            //     console.log(variable_name)
            //
            //     currentRow.each(function () {
            //         // let inputName = variable_name + '[' + t_array[i++] + ']';
            //
            //         $(this).removeAttr('name').attr("name", `${variable_name}[${t_array[i++]}]`);
            //     });
            // });

            $(document).on('change','.checkbox_or_numeric_select',function(){
                let value = $(this).val().toLowerCase().replaceAll(" ","_");

                let variable_name = $(this).closest(".append-include").find('.checkbox_or_numeric_title').val().replaceAll(" ","_");
                let currentRow = $(this).closest(".append-include").find("td");
                let add_minus_button = `
                    <div class="package-button-wrapper">
                         <div class="package-field-icon add-rows">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <div class="package-field-icon remove-rows remove-icon">
                            <i class="fa-solid fa-minus"></i>
                        </div>
                    </div>
                `;

                let t_array = [
                    "basic",
                    "standard",
                    "premium"
                ];
                let i = 0;

                currentRow.each(function (){
                    let row_type = t_array[i++];
                    let inputName = variable_name + '['+ row_type +']';

                    let checkbox, number;


                    if($('#offer_packages_available_or_not').val() != 1 && i > 1){
                        checkbox = '<input type="checkbox" name="'+ inputName +'" class="check-input disabled_or_not" checked>';
                        number = '<input type="number" name="'+ inputName +'" class="form-control disabled_or_not" value="5">';
                    }else{
                        checkbox = '<input type="checkbox" name="'+ inputName +'" class="check-input" checked>';
                        number = '<input type="number" name="'+ inputName +'" class="form-control" value="5">';
                    }

                    if(row_type == 'premium'){
                        checkbox  = checkbox + add_minus_button;
                        number  = number + add_minus_button;
                    }

                    if(value == 'checkbox'){
                        $(this).html(checkbox);
                    }else{
                        $(this).html(number);
                    }

                    if($('#offer_packages_available_or_not').val() == 1){
                        $('.disabled_or_not'). prop('disabled', false);
                    }else{
                        $('.disabled_or_not'). prop('disabled', true);
                    }
                })
            });


            // checkbox numeric title get and set
            $(document).on('keyup','.checkbox_or_numeric_title',function(){
                let check_numeric_title = $(this).text();
                $('#check_numeric_title').val(check_numeric_title);
            });

            //remove row
            $(document).on('click', '.remove-icon', function() {
                $(this).closest('.append-remove').remove();
            });

            //add row
            $(document).on('click', '.add-rows', function() {
                let tableData = `
                    <tr class="append-include append-remove">
                       <th>
                            <div class="package-head-left">
                                <div class="package-head-left-flex flex-column">
                                    <input class="form-control checkbox_or_numeric_title" type="text" name="checkbox_or_numeric_title[]" placeholder="{{ __('Enter Title') }}">
                                    <div class="text-danger validation-error"></div>
                                </div>
                                <div class="package-field">
                                    <div class="package-field-select">
                                        <select class="form-control checkbox_or_numeric_select" name="checkbox_or_numeric_select[]">
                                            <option value="checkbox">{{ __('Check Boxes') }}</option>
                                            <option value="numeric">{{ __('Numeric') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </th>

                        <td>
                                <input type="checkbox" name="title[basic]" class="check-input" checked>
                        </td>

                        <td>
                                <input name="title[standard]" type="checkbox" class="check-input disabled_or_not" checked>
                        </td>

                        <td>
                            <input name="title[premium]" type="checkbox" class="check-input disabled_or_not" checked>

                            <div class="package-button-wrapper">
                                <div class="package-field-icon add-rows">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                                <div class="package-field-icon remove-rows remove-icon">
                                    <i class="fa-solid fa-minus"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
            `;

                $('.create_project_table tr:last').prev().after(tableData);
                $('.package-field-input .disabled_or_not').remove();

                if($('#offer_packages_available_or_not').prop('checked')){
                    $('.create_project_table .disabled_or_not'). prop('disabled', false);
                }else{
                    $('.create_project_table .disabled_or_not'). prop('disabled', true);
                }
            });

                document.querySelector('#upload_project_photo').addEventListener('change', function () {
                const files = this.files;
                const previewArea = document.querySelector('.file-preview-area');

                const allowedImages = ['jpg', 'jpeg', 'png', 'svg', 'bmp', 'tiff', 'webp'];
                const allowedVideos = ['mp4', 'mov', 'avi', 'mkv', 'wmv'];
                const allowedDocs = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];

                var maxFiles = 5;
                var existingCount = parseInt($(this).data('existing-count')) || 0;
                var selectedCount = this.files.length;
                var total = existingCount + selectedCount;

                if (total > maxFiles) {
                    toastr.error('You can have a maximum of 5 images in total.');
                    this.value = '';
                    // Only remove new previews, not already-uploaded ones
                    $('.file-preview-area .js-new-preview').remove();
                    return false;
                }

                // Remove any previous new previews before adding new ones
                $('.file-preview-area .js-new-preview').remove();

                Array.from(files).forEach(file => {
                    const ext = file.name.split('.').pop().toLowerCase();
                    const fileURL = URL.createObjectURL(file);

                    let content = '';

                    if (allowedImages.includes(ext)) {
                        content = `<img src="${fileURL}" class="project_photo_preview">`;
                    } else if (allowedVideos.includes(ext)) {
                        content = `
                            <video controls class="project_photo_preview" style="max-height: 200px;">
                                <source src="${fileURL}" type="video/${ext}">
                            </video>`;
                    } else if (allowedDocs.includes(ext)) {
                        content = `
                            <div class="file-name-box">
                                <i class="fa fa-file"></i>
                                <span class="file-name-text">${file.name}</span>
                            </div>`;
                    } else {
                        content = `<p class="text-muted">${file.name} — No preview available</p>`;
                    }

                    // Add js-new-preview class to new previews
                    previewArea.insertAdjacentHTML('beforeend', `
                        <div class="file-preview-wrapper mb-2 js-new-preview">
                            <button type="button" class="remove-existing-file">&times;</button>
                            ${content}
                        </div>
                    `);
                });
            });

            // basic price setup
            $(document).on('click','.basic_price_setup',function(){
                let basic_discount_charge = $('#basic_discount_charge').val();
                let basic_regular_charge = $('#basic_regular_charge').val();
                if(basic_discount_charge != ''){
                    $('.basic_discount_charge').html('<span class="basic_discount_charge">' + '{{ site_currency_symbol() }}' + basic_discount_charge + '</span>');
                    $('.basic_regular_charge').html('<span class="basic_regular_charge"><s>' + '{{ site_currency_symbol() }}' +basic_regular_charge + '</s></span>');
                }else{
                    $('.basic_discount_charge').html('');
                    $('.basic_regular_charge').html('<span class="basic_regular_charge">' + '{{ site_currency_symbol() }}' + basic_regular_charge + '</span>');
                }
                $('.price-popup-basic-charge, .popup-overlay').removeClass('popup-active');
            })

            // basic price setup
            $(document).on('click','.standard_price_setup',function(){
                let standard_regular_charge = $('#standard_regular_charge').val();
                let standard_discount_charge = $('#standard_discount_charge').val();
                if(standard_discount_charge != ''){
                    $('.standard_discount_charge').html('<span class="standard_discount_charge">' + '{{ site_currency_symbol() }}' + standard_discount_charge + '</span>');
                    $('.standard_regular_charge').html('<span class="standard_regular_charge"><s>' + '{{ site_currency_symbol() }}' +standard_regular_charge + '</s></span>');
                }else{
                    $('.standard_discount_charge').html('');
                    $('.standard_regular_charge').html('<span class="standard_regular_charge">' + '{{ site_currency_symbol() }}' + standard_regular_charge + '</span>');
                }
                $('.price-popup-standard-charge, .popup-overlay').removeClass('popup-active');
            })

            // basic price setup
            $(document).on('click','.premium_price_setup',function(){
                let premium_discount_charge = $('#premium_discount_charge').val();
                let premium_regular_charge = $('#premium_regular_charge').val();
                if(premium_discount_charge != ''){
                    $('.premium_discount_charge').html('<span class="premium_discount_charge">' + '{{ site_currency_symbol() }}' + premium_discount_charge + '</span>');
                    $('.premium_regular_charge').html('<span class="premium_regular_charge"><s>' + '{{ site_currency_symbol() }}' +premium_regular_charge + '</s></span>');
                }else{
                    $('.premium_discount_charge').html('');
                    $('.premium_regular_charge').html('<span class="premium_regular_charge">' + '{{ site_currency_symbol() }}' + premium_regular_charge + '</span>');
                }
                $('.price-popup-premium-charge, .popup-overlay').removeClass('popup-active');
            })

            $(document).on('keyup','.checkbox_or_numeric_title',async function(){
                let variable_name = $(this).val().replaceAll(" ","_").toLowerCase();
                let currentRow = $(this).closest(".append-include").find("td input");
                let arrVal = [];

                $(this).parent().find('.validation-error').text("");

                validation.project_title_error = false;

                await $(`.checkbox_or_numeric_title`).each(function (){
                    if(arrVal.includes($(this).val())) {
                        validation.project_title_error = true;
                        arrVal.push($(this).val());

                        if($(this).val().length > 0){
                            $(this).parent().find('.validation-error').text("{{ __("This title is already in use.") }}");
                        }else{
                            $(this).parent().find('.validation-error').text("{{ __("This field is required.") }}");
                        }
                    }else{
                        if($(this).val().length < 1){
                            validation.project_title_error = true;
                            $(this).parent().find('.validation-error').text("{{ __("This field is required.") }}");
                        }
                        arrVal.push($(this).val());
                    }
                });

                let t_array = [
                    "basic",
                    "standard",
                    "premium"
                ];
                let i = 0;

                currentRow.each(function () {
                    let inputName = variable_name + '[' + t_array[i++] + ']';
                    $(this).attr("name", inputName);
                });
            });

            function titleShouldBeUnique(){
                toastr_warning_js("{{ __("All package title is required and title must be unique.") }}");
            }

            // create project
            $(document).on('click', '#confirm_create_project', async function(e) {
                let basic_title = $('#basic_title').text();
                let standard_title = $('#standard_title').text();
                let premium_title = $('#premium_title').text();
                let checkbox_or_numeric_title = $('.checkbox_or_numeric_title').val();

                $('#set_basic_title').val(basic_title);
                $('#set_standard_title').val(standard_title);
                $('#set_premium_title').val(premium_title);

                // Start Package Details 50 Char Validation
                let limitExceeded = [];

                let basic_details = $('#basic_details').val()?.trim();
                let standard_details = $('#standard_details').val()?.trim();
                let premium_details = $('#premium_details').val()?.trim();

                if (basic_details && basic_details.length > 150) limitExceeded.push("Basic");
                if (standard_details && standard_details.length > 150) limitExceeded.push("Standard");
                if (premium_details && premium_details.length > 150) limitExceeded.push("Premium");

                if (limitExceeded.length > 0) {
                    let message = limitExceeded.join(", ") + " package" + (limitExceeded.length > 1 ? "s have" : " has") + " more than 150 characters.";
                    toastr_warning_js(message);
                    e.preventDefault();
                    return false;
                }
                // End Package Details Validation

                let selfError = { error: false };
                let arrVal = [];
                let validation = false;

                await $('.checkbox_or_numeric_title').each(function () {
                    if (arrVal.includes($(this).val())) {
                        validation = true;
                    } else {
                        if ($(this).val().length < 1) {
                            validation = true;
                            $(this).parent().find('.validation-error').text("{{ __('This field is required.') }}");
                        }
                        arrVal.push($(this).val());
                    }
                });

                if (validation) {
                    titleShouldBeUnique();
                    e.preventDefault();
                    return false;
                } else {
                    check_package_titles.status = true;
                }

                if (selfError.error) {
                    titleShouldBeUnique();
                    return false;
                }

                if (check_package_titles.status) {
                    $('#project_edit_load_spinner').html('<i class="fas fa-spinner fa-pulse"></i>');
                } else {
                    return false;
                }
            });


            $(document).on('change', '#upload_project_photo', function () {
                var maxFiles = 5;
                var existingCount = parseInt($(this).data('existing-count')) || 0;
                var selectedCount = this.files.length;
                var total = existingCount + selectedCount;

                if (total > maxFiles) {
                    toastr.error('You can have a maximum of 5 images in total.');
                    this.value = '';
                    // Only remove new previews, not already-uploaded ones
                    $('.file-preview-area .js-new-preview').remove();
                    return false;
                }

                // Remove any previous new previews before adding new ones
                $('.file-preview-area .js-new-preview').remove();

                const files = this.files;
                const previewArea = document.querySelector('.file-preview-area');
                const allowedImages = ['jpg', 'jpeg', 'png', 'svg', 'bmp', 'tiff', 'webp'];
                const allowedVideos = ['mp4', 'mov', 'avi', 'mkv', 'wmv'];
                const allowedDocs = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];

                Array.from(files).forEach(file => {
                    const ext = file.name.split('.').pop().toLowerCase();
                    const fileURL = URL.createObjectURL(file);
                    let content = '';
                    if (allowedImages.includes(ext)) {
                        content = `<img src="${fileURL}" class="project_photo_preview">`;
                    } else if (allowedVideos.includes(ext)) {
                        content = `
                            <video controls class="project_photo_preview" style="max-height: 200px;">
                                <source src="${fileURL}" type="video/${ext}">
                            </video>`;
                    } else if (allowedDocs.includes(ext)) {
                        content = `
                            <div class="file-name-box">
                                <i class="fa fa-file"></i>
                                <span class="file-name-text">${file.name}</span>
                            </div>`;
                    } else {
                        content = `<p class="text-muted">${file.name} — No preview available</p>`;
                    }
                    // Add js-new-preview class to new previews
                    previewArea.insertAdjacentHTML('beforeend', `
                        <div class="file-preview-wrapper mb-2 js-new-preview">
                            <button type="button" class="remove-existing-file">&times;</button>
                            ${content}
                        </div>
                    `);
                });
            });

        });
    }(jQuery));

    function pre_next()
    {
        let Listings = document.querySelectorAll(".single-setup-request-list li");
        let sections = document.querySelectorAll(".setup-wrapper-contents");
        let current = 0;

        const toggleListings = () => {
            Listings.forEach(function(e) {
                e.classList.remove('running');
            });
            Listings[current].classList.add("running");
            Listings[current].classList.remove("completed");
            if (current != 0) {
                Listings[current - 1].classList.add("completed");
            }
        }

        const toggleSections = () => {
            sections.forEach(function(section) {
                section.classList.remove('active');
            });
            sections[current].classList.add("active");
        }

        $(document).on("click", "#next", function (e){
            e.preventDefault();

            if (current <= Listings.length) {
                current++

                // todo add introduction
                if(current == 1){
                    let title = $('#project_title').val();
                    let description = $('#project_description').val();
                    let category = $('#category').val();
                    let subcategory = $('#subcategory').val();

                    if(title == '' || description == '' || category == '' || subcategory == ''){
                        current = 0;
                        toastr_warning_js("{{ __('Please fill all fields !') }}");
                        return false;
                    }
                    if(title.length < 5){
                        current = 0;
                        toastr_warning_js("{{ __('Title must be at least 5 characters') }}");
                        return false;
                    }
                    if(description.length < 10){
                        current = 0;
                        toastr_warning_js("{{ __('Description must be at least 10 characters') }}");
                        return false;
                    }
                }
                else if(current == 2){
                    let image = $('#upload_project_photo').val();
                    @if(moduleExists('SecurityManage'))
                        @if(Auth::guard('web')->user()->freeze_project == 'freeze')
                            $('.setup-footer-right').html('<a href="#" class="btn-profile btn-bg-1 @if(Auth::guard('web')->user()->freeze_project == 'freeze') disabled-link @endif">{{ __('Update Project') }}</a>');
                        @else
                            $('.setup-footer-right').html('<button type="submit" class="btn-profile btn-bg-1" id="confirm_create_project">{{ __('Update Project') }}<span id="project_edit_load_spinner"></span></button>');
                        @endif
                    @else
                        $('.setup-footer-right').html('<button type="submit" class="btn-profile btn-bg-1" id="confirm_create_project">{{ __('Update Project') }}<span id="project_edit_load_spinner"></span></button>');
                    @endif
                }else{
                    $('.setup-footer-right').html('<a href="javascript:void(0)" class="setup-footer-next next" id="next"> <i class="fas fa-arrow-right"></i> </a>');
                }
            }

            toggleListings();
            toggleSections();
        })

        $(document).on("click", "#previous", function (){
            if (current > 0) {
                current--
                if(current == 2){
                    $('.setup-footer-right').html('<input type="submit" class="btn-profile btn-bg-1" value="{{ __('Update Project') }}">');
                }else{
                    $('.setup-footer-right').html('<a href="javascript:void(0)" class="setup-footer-next next" id="next"> <i class="fas fa-arrow-right"></i> </a>');
                }
            }
            toggleListings();
            toggleSections();
        });
    }

    // todo toastr warning
    function toastr_warning_js(msg){
        Command: toastr["warning"](msg, "Warning !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
    //toastr success
    function toastr_success_js(msg){
        Command: toastr["success"](msg, "Success !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }

</script>
