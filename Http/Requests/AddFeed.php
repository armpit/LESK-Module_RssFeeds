<?php

namespace App\Modules\RssFeeds\Http\Requests;

use App\Http\Requests\Request;
use Auth;

class AddFeed extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::user())
            return true;
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'txtName' => 'bail|required|max:255',
            'txtUrl'  => 'bail|required|url',
        ];
    }
}
