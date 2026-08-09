<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Services\FormBuilderService;
use App\Models\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormBuilderController extends Controller
{
    public function form(Request $request)
    {
        if($request->isMethod('post')){
            return (new FormBuilderService())->form($request);
        }
        $all_forms = FormBuilder::latest()->get();
        return view('backend.forms.all-forms',compact('all_forms'));
    }

    public function edit_form(Request $request, $id)
    {
        $form =  FormBuilder::findOrFail($id);
        return view('backend.forms.edit-form',compact('form'));
    }

    public function update_form(Request $request){
        $this->validate($request,[
            'title' => 'required|string',
            'email' => 'required|string',
            'button_title' => 'required|string',
            'field_name' => 'required|max:191',
            'field_placeholder' => 'required|max:191',
            'success_message' => 'required',
        ]);
        $id = $request->id;
        $title = $request->title;
        $email = $request->email;
        $button_title = $request->button_title;
        $success_message = $request->success_message;

        $field_types = $request->field_type ?? [];
        $field_names = $request->field_name ?? [];
        $field_placeholders = $request->field_placeholder ?? [];
        $field_requireds = $request->field_required ?? [];
        $mimes_types = $request->mimes_type ?? [];
        $select_options_raw = $request->select_options ?? [];

        $all_fields_name = [];
        foreach ($field_names as $fname){
            $all_fields_name[] = strtolower(Str::slug($fname));
        }

        $select_options_mapped = [];
        $select_index = 0;
        foreach ($field_types as $key => $type) {
            if ($type === 'select') {
                $fname = $all_fields_name[$key] ?? '';
                if (!empty($fname)) {
                    $raw_opts = $select_options_raw[$select_index] ?? '';
                    // Convert newline-separated string to array, trimming and filtering empty entries
                    $opts_array = array_filter(array_map('trim', explode("\n", $raw_opts)), function($value) {
                        return $value !== '';
                    });
                    // Reindex array keys
                    $select_options_mapped[$fname] = array_values($opts_array);
                }
                $select_index++;
            }
        }

        $fields_data = [
            'success_message' => $success_message,
            'field_type' => $field_types,
            'field_name' => $all_fields_name,
            'field_placeholder' => $field_placeholders,
            'field_required' => (object)$field_requireds,
            'mimes_type' => (object)$mimes_types,
            'select_options' => (object)$select_options_mapped
        ];

        $json_encoded_data = json_encode($fields_data);

        FormBuilder::findOrfail($id)->update([
            'title' => $title,
            'email' => $email,
            'button_text' => $button_title,
            'success_message' => $success_message,
            'fields' => $json_encoded_data
        ]);

        toastr_success(__('Item Updated Successfully'));
        return back();
    }

    public function delete_form($id){
        return (new FormBuilderService())->delete_form($id);
    }

    public function bulk_action(Request $request){
        return (new FormBuilderService())->bulk_action($request);
    }
}
