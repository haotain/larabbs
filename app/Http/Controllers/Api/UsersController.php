<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = Cache::get($request->verification_key);

        if (!$verifyData) {
            abort('403', '验证码失效');
        }

        if (!hash_equals((string)$verifyData['code'], $request->verification_code)) {

            throw new AuthenticationException('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => $request->password,
        ]);

        Cache::forget($request->verifation_key);

        return (new UserResource($user))->showSensitiveFields();
    }

    /**
     * 某个用户的详情
     */
    public function show(User $user, Request $request)
    {
        return new UserResource($user);
    }

    /**
     * 当前登录用户信息
     */
    public function me(Request $request)
    {
        return (new UserResource($request::user()))->showSensitiveFields();
    }

    /**
     * 编辑登录用户信息
     */
    public function update(UserRequest $request)
    {

        $user = $request->user();

        $attributes = $request->only(['name', 'email', 'introduction', 'registration_id']);

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);

            $attributes['avatar'] = $image->path;
        }

        $user->update($attributes);

        return (new UserResource($user))->showSensitiveFields();
    }

    /**
     * 活跃用户
     */
    public function activedIndex(User $user)
    {
        UserResource::wrap('data');
        return UserResource::collection($user->getActiveUsers());
    }
}
