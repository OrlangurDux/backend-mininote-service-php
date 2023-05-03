<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $title
 * @property string $note
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Note extends Model{
    //
    protected function categoryId(): Attribute{
        return Attribute::make(
            set: fn(string $value) => $value == '' ? NULL : $value
        );
    }
}
