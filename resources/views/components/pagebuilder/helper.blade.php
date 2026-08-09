
<script>
    /*-----------------------------------
    *   RANGE SCRIPT
    * ---------------------------------*/
    $(document).on('change','.page-builder-area-wrapper input[type="range"]',function (e){
    e.preventDefault();
    var el = $(this);
    el.next('.range-val').text(el.val()+el.data('unit-type'));
    });



    /*-----------------------------------
    *  COLOR Picker INIT FUnction
    * ---------------------------------*/

    function colorPickerInit(selector){

    $.each(selector,function (index,value){
    var el = $(this);
    el.spectrum({
    showAlpha: true,
    showPalette: true,
    cancelText : '',
    showInput: true,
    allowEmpty:true,
    chooseText : '',
    maxSelectionSize: 2,
    color: el.next('input').val(),
    change: function(color) {
    el.next('input').val( color ? color.toRgbString() : '');
    el.css({
    'background-color' : color ? color.toRgbString() : ''
    });
    },
    move: function(color) {
    el.next('input').val( color ? color.toRgbString() : '');
    el.css({
    'background-color' : color ? color.toRgbString() : ''
    });
    },
    palette: [
    [
    "{{get_static_option('site_color')}}",
    "{{get_static_option('site_main_color_two')}}",
    "{{get_static_option('site_secondary_color')}}",
    "{{get_static_option('site_heading_color')}}",
    "{{get_static_option('site_paragraph_color')}}",
    "{{get_static_option('portfolio_home_color')}}",
    "{{get_static_option('logistics_home_color')}}",
    "{{get_static_option('industry_home_color')}}",
    "{{get_static_option('construction_home_color')}}",
    "{{get_static_option('lawyer_home_color')}}",
    "{{get_static_option('political_home_color')}}",
    "{{get_static_option('medical_home_color')}}",
    "{{get_static_option('medical_home_color_two')}}",
    "{{get_static_option('fruits_home_color')}}",
    "{{get_static_option('fruits_home_heading_color')}}",
    "{{get_static_option('portfolio_home_dark_color')}}",
    "{{get_static_option('portfolio_home_dark_two_color')}}",
    "{{get_static_option('charity_home_color')}}",
    "{{get_static_option('dagency_home_color')}}",
    "{{get_static_option('cleaning_home_color')}}",
    "{{get_static_option('cleaning_home_two_color')}}",
    "{{get_static_option('course_home_color')}}",
    "{{get_static_option('grocery_home_two_color')}}",
    "{{get_static_option('grocery_home_color')}}"
    ]
    ]
    });

    el.on("dragstop.spectrum", function(e, color) {
    el.next('input').val( color.toRgbString());
    el.css({
    'background-color' : color.toHexString()
    });
    });
    });
    }

    /*------------------------------------------
    *   ICON PICKET INIT
    * ----------------------------------------*/
    $('.icp-dd').iconpicker();
    $('body').on('iconpickerSelected','.icp-dd', function (e) {
    var selectedIcon = e.iconpickerValue;
    $(this).parent().parent().children('input').val(selectedIcon);
    $('body .dropdown-menu.iconpicker-container').removeClass('show');
    });

    /*-------------------------------------------
    *   REPEATER SCRIPT & SORTABLE
    * ------------------------------------------*/
    function initRepeaterSortable(element) {
        if (jQuery.ui && jQuery.ui.sortable) {
            var target = element || $('.iconbox-repeater-wrapper');
            target.each(function() {
                var el = $(this);
                if (!el.hasClass('ui-sortable')) {
                    el.sortable({
                        handle: ".action-wrap",
                        axis: "y",
                        placeholder: "ui-state-highlight",
                        cursor: "move",
                        forcePlaceholderSize: true
                    });
                    el.find('.action-wrap').css('cursor', 'move');
                } else {
                    el.sortable("refresh");
                }
            });
        }
    }

    $(document).ready(function() {
        initRepeaterSortable();
    });

    $(document).on('click', '.expand', function() {
        setTimeout(function() {
            initRepeaterSortable();
        }, 150);
    });

    $(document).on('click','.all-field-wrap .action-wrap .add',function (e){
        e.preventDefault();

        var el = $(this);
        var parent = el.parent().parent();
        var container = parent.parent().find('.all-field-wrap');
        var clonedData = parent.clone();
        var containerLength = container.length;
        
        clonedData.find('#myTab').attr('id','mytab_'+containerLength);
        clonedData.find('#myTabContent').attr('id','myTabContent_'+containerLength);
        var allTab =  clonedData.find('.tab-pane');
        allTab.each(function (index,value){
            var el = $(this);
            var oldId = el.attr('id');
            el.attr('id',oldId+containerLength);
        });
        var allTabNav =  clonedData.find('.nav-link');
        allTabNav.each(function (index,value){
            var el = $(this);
            var oldId = el.attr('href');
            el.attr('href',oldId+containerLength);
        });

        clonedData.insertAfter(parent);

        if (containerLength > 0){
            parent.parent().find('.remove').show(300);
        }
        
        parent.parent().find('.icp-dd').iconpicker('destroy');
        parent.parent().find('.icp-dd').iconpicker();

        initRepeaterSortable(parent.parent());
    });

    $(document).on('click','.all-field-wrap .action-wrap .remove',function (e){
        e.preventDefault();
        var el = $(this);
        var parent = el.parent().parent();
        var wrapper = parent.parent();
        var container = wrapper.find('.all-field-wrap');

        if (container.length > 1){
            el.show(300);
            parent.hide(300, function() {
                parent.remove();
                initRepeaterSortable(wrapper);
            });
        }else{
            el.hide(300);
        }
    });

    function strRand(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() *
            charactersLength));
        }
        return result;
    }
</script>
