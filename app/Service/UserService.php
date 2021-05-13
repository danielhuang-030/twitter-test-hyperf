<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Service;

use App\Model\User;
use HyperfExt\Hashing\Hash;

class UserService
{
    /**
     * create user.
     *
     * @return User
     */
    public function createUser(array $data): ?User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    /**
     * attempt.
     *
     * @return User
     */
    public function attempt(array $credentials): ?User
    {
        $user = User::where('email', $credentials['email'])->first();
        if ($user === null) {
            return null;
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * get user.
     *
     * @return User
     */
    public function getUser(int $id): ?User
    {
        return User::find($id);
    }

    // /**
    //  * get posts.
    //  *
    //  * @return LengthAwarePaginator
    //  */
    // public function getPosts(PostParam $param)
    // {
    //     return $this->postRepository->getByParam($param);
    // }
}
