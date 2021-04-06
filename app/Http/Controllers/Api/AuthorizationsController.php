<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationsRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Overtrue\LaravelSocialite\Socialite;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationsController extends AccessTokenController
{
    /**
     * 第三方登录授权
     * 改用passport 自定义方式授权登录
     */
    // public function socialStore($type, SocialAuthorizationRequest $request)
    // {
    //     $driver = Socialite::create($type);

    //     try {

    //         if ($code = $request->code) {

    //             $oauthUser = $driver->userFromCode($code);

    //         } else {

    //             $tokenData['access_token'] = $request->access_token;

    //             // 微信需要增加openid
    //             if ($type == 'wechat') {
    //                 $driver->withOpenid($request->openid);
    //             }

    //             $oauthUser = $driver->userFromToken($request->access_token);
    //         }

    //     } catch (\Exception $e) {
    //         throw new AuthenticationException('参数错误, 为获取用户信息');
    //     }

    //     if (!$oauthUser->getId()) {
    //         throw new AuthenticationException('参数错误，未获取用户信息');
    //     }

    //     switch ($type) {
    //         case 'wechat':
    //             $unionid = $oauthUser->getRaw()['unionid'] ?? null;

    //             if ($unionid) {
    //                 $user = User::where('weixin_unionid', $unionid)->first();
    //             } else {
    //                 $user = User::where('weixin_openid', $oauthUser->getId())->first();
    //             }

    //             // 没有用户，默认创建一个用户
    //             if (!$user) {
    //                 $user = User::create([
    //                     'name' => $oauthUser->getNickname(),
    //                     'avatar' => $oauthUser->getAvatar(),
    //                     'weixin_openid' => $oauthUser->getId(),
    //                     'weixin_unionid' => $unionid,
    //                 ]);
    //             }
    //             break;

    //     }
    //     $token = auth('api')->login($user);
    //     return $this->respondWithToken($token)->setStatusCode(201);
    // }



    /**
     * passport oauth:password 授权登录
     */
    public function store(ServerRequestInterface $request)
    {
        return $this->issueToken($request)->setStatusCode(201);
    }

    /**
     * passport 刷新token
     */
    public function update(ServerRequestInterface $request)
    {
        return $this->issueToken($request);
    }

    /**
     * passport 删除token
     */
    public function destroy()
    {
        if (auth('api')->check()) {
            auth('api')->user()->token()->revoke();
            return response(null, 204);
        } else {
            throw new AuthenticationException('The token is invalid.');
        }
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    //-----------------jwt auth 使用下面方法------------------------------
    /**
     * 用户名密码登录
     */
    // public function store(AuthorizationsRequest $request)
    // {
    //     $username = $request->username;

    //     filter_var($username, FILTER_VALIDATE_EMAIL) ? $credentials['email'] = $username : $credentials['phone'] = $username;

    //     $credentials['password'] = $request->password;

    //     if (!$token = \Auth::guard('api')->attempt($credentials)) {
    //         throw new AuthenticationException(trans('auth.failed'));
    //     }

    //     return $this->respondWithToken($token)->setStatusCode(201);
    // }

    /**
     * 刷新token
     */
    // public function update()
    // {
    //     $token = auth('api')->refresh();
    //     return $this->respondWithToken($token);
    // }

    /**
     * 删除token
     */
    // public function destroy()
    // {
    //     auth('api')->logout();
    //     return response(null, 204);
    // }

     //---------------------------------------------------------
}
