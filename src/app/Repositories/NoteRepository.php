<?php
namespace App\Repositories;

use App\Http\Resources\UniversalDTO;
use App\Interfaces\Repository\NoteRepositoryInterface;
use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Mockery\Matcher\Not;

class NoteRepository implements NoteRepositoryInterface{

    /**
     * @return Collection
     */
    public function getAllNotes(): Collection{
        return Note::whereUserId(Auth::id())->get();
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return Collection
     */
    public function getNotes(int $offset, int $limit): Collection{
        return Note::whereUserId(Auth::id())->offset($offset)->limit($limit)->get();
    }

    /**
     * @param int $id
     * @param bool $checkPermission
     * @return Note
     */
    public function getById(int $id, bool $checkPermission = true): Note{
        if($checkPermission){
            $element = Note::whereUserId(Auth::id())->whereId($id)->first();
        }else{
            $element = Note::whereId($id)->first();
        }
        return $element;
    }
}
