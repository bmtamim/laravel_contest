<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $contestNoUnique = Rule::unique('contests');
        $imageRule = 'required';
        if ($this->route()->hasParameter('contest')) {
            $contestNoUnique = Rule::unique('contests')->ignoreModel($this->route()->parameter('contest'));
            $imageRule = 'nullable';
        }
        return [
            'contest_no'          => ['required', 'string', $contestNoUnique],
            'title'               => ['required', 'string',],
            'description'         => ['nullable', 'string',],
            'short_description'   => ['nullable', 'string',],
            'competition_details' => ['required', 'string',],
            'ticket_price'        => ['required', 'numeric',],
            'ticket_quantity'     => ['required', 'integer',],
            'image'               => [$imageRule, 'image', 'mimes:png,jpg,jpeg,gif,svg', 'max:510'], //max file size 510kb
            'competition_date'    => ['required', 'string',],
            'status'              => ['nullable', 'string',],
            'categories'          => ['required', 'array',],
            'categories.*'        => ['numeric',],
            'image_gallery'       => ['nullable'],
            'image_gallery.*'     => ['image', 'mimes:png,jpg,jpeg,gif,svg', 'max:510'],//max file size 510kb
        ];
    }
}
