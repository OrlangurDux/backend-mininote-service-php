<?php
namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class RestoreTokenRule implements Rule {

    private $restoreToken;

    public function __construct(){
        $this->restoreToken = request()->get("restore_token");
    }

    public function passes($attribute, $value): bool{
        $check = true;
        if($this->restoreToken != ""){
            $currentRestoreToken = User::whereRestoreToken($this->restoreToken)->first();
            if($currentRestoreToken == null){
                $check = false;
            }
        }
        return $check;
    }

    public function message(): string{
        return __('user.error_restore_token_invalid');
    }
}
