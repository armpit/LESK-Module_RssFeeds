<?php

namespace App\Modules\RssFeeds\Http\Requests;

use App\Http\Requests\Request;
use Auth;

class EditSettings extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::user()->hasRole('admins'))
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
            'cache_enable'  => 'required|Boolean',
            'cache_dir'  => 'required',
            'cache_ttl'  => 'required|Integer',
            'personal_enable'  => 'required|Boolean',
        ];
    }
}
