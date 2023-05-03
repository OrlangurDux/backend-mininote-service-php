<?php
namespace App\Interfaces\Api\V1;

use App\Http\Requests\Api\V1\NoteRequest;
use App\Http\Resources\UniversalDTO;

interface NoteInterface {
    public function list(NoteRequest $request): UniversalDTO;
    public function read(NoteRequest $request): UniversalDTO;
    public function create(NoteRequest $request): UniversalDTO;
    public function update(NoteRequest $request): UniversalDTO;
    public function delete(NoteRequest $request): UniversalDTO;
}
