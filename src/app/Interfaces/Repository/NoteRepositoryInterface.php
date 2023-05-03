<?php
namespace App\Interfaces\Repository;

use App\Http\Resources\UniversalDTO;
use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;

interface NoteRepositoryInterface {
    public function getAllNotes(): Collection;
    public function getNotes(int $offset, int $limit): Collection;
    public function getById(int $id, bool $checkPermission): Note;
}
