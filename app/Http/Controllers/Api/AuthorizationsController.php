<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationsRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Models\User;
use EasyWeChatComposer\EasyWeChat;
use Illuminate\Auth\AuthenticationException;
use Overtrue\LaravelSocialite\Socialite;

class AuthorizationsController extends Controller
{
    /**
     * 第三方登录授权
     */
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        $driver = Socialite::create($type);

        try {

            if ($code = $request->code) {

                $oauthUser = $driver->userFromCode($code);

            } else {

                $tokenData['access_token'] = $request->access_token;

                // 微信需要增加openid
                if ($type == 'wechat') {
                    $driver->withOpenid($request->openid);
                }

                $oauthUser = $driver->userFromToken($request->access_token);
            }

        } catch (\Exception $e) {
            throw new AuthenticationException('参数错误, 为获取用户信息');
        }

        if (!$oauthUser->getId()) {
            throw new AuthenticationException('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'wechat':
                $unionid = $oauthUser->getRaw()['unionid'] ?? null;

                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                // 没有用户，默认创建一个用户
                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }
                break;

        }
        $token = auth('api')->login($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    /**
     * 登录
     */
    public function store(AuthorizationsRequest $request)
    {
        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ? $credentials['email'] = $username : $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            throw new AuthenticationException(trans('auth.failed'));
        }

        return $this->respondWithToken($token)->setStatusCode(201);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * 刷新token
     */
    public function update()
    {
        $token = auth('api')->refresh();
        return $this->respondWithToken($token);
    }

    /**
     * 删除token
     */
    public function destory()
    {
        auth('api')->logout();
        return response(null, 204);
    }

    /**
     * 小程序登录
     */
    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;


        // 根据code 获取 openid 和 session_Key
        $minaProgram = \EasyWeChat::miniProgram();

        $data = $minaProgram->auth->session($code);

        // 如果结果错误， 说明 code 已过期或者不正确， 返回 401 错误
        if (isset($data['errcode'])) {
            throw new AuthenticationException('code 不正确');
        }

        // 找到 openid 对应的用户
        $user = User::where('weapp_openid', $data['openid'])->first();

        $attributes['weixin_session_key'] = $data['session_key'];

        // 未找到对应的用户则需要提交用户名密码进行用户绑定
        if (!$user) {
            // 如果未提交用户名和密码， 403 错误
            if (!$request->username) {
                throw new AuthenticationException('用户不存在');
            }

            $username = $request->username;

            filter_var($username, FILTER_VALIDATE_EMAIL) ?  $credentials['email'] = $username : $credentials['phone'] = $username;

            $credentials['password'] = $request->password;

            // 验证用户名和密码是否正确
            if (!auth('api')->once($credentials)) {
                throw new AuthenticationException('用户名或密码错误');
            }

            // 获取对应的用户
            $user = auth('api')->getUser();
            $attributes['weapp_openid'] = $data['openid'];
        }

        // 更新用户数据
        $user->update($attributes);

        // 为对应应用创建 JWT
        $token = auth('api')->login($user);

        return $this->respondWithToken($token)->setStatusCode(201);
    }
}
