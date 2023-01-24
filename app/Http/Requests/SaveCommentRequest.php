<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveCommentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'data.attributes.body' => ['required'],
            'data.relationships.article.data.id' => [
                'required',
                Rule::exists('articles', 'slug'),
            ],
            'data.relationships.author.data.id' => [
                'required',
                Rule::exists('users', 'id'),
            ],
        ];
    }
}
