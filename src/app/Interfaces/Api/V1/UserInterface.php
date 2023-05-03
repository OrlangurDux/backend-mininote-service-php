<?php
namespace App\Interfaces\Api\V1;

use App\Http\Requests\Api\V1\UserRequest;
use App\Http\Resources\UniversalDTO;

interface UserInterface {
    /**
     * @param UserRequest $request
     * @return UniversalDTO
     */
    public function checkByEmail(UserRequest $request): UniversalDTO;

    /**
     * @param UserRequest $request
     * @return UniversalDTO
     */
    public function register(UserRequest $request): UniversalDTO;

    /**
     * @param UserRequest $request
     * @return UniversalDTO
     */
    public function login(UserRequest $request): UniversalDTO;

    /**
     * @param UserRequest $request
     * @return UniversalDTO
     */
    public function forgot(UserRequest $request): UniversalDTO;

    /**
     * @param UserRequest $request
     * @return UniversalDTO
     */
    public function readProfile(UserRequest $request): UniversalDTO;

    /**
     * @param UserRequest $request
     * @return UniversalDTO
     */
    public function updateProfile(UserRequest $request): UniversalDTO;

    /**
     * @param UserRequest $request
     * @return UniversalDTO
     */
    public function deleteProfile(UserRequest $request): UniversalDTO;

    /**
     * @param UserRequest $request
     * @return UniversalDTO
     */
    public function updatePassword(UserRequest $request): UniversalDTO;
}
