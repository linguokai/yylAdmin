<?php
/*
 * @Description  : token
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-04-16
 */

namespace app\admin\service;

use Firebase\JWT\JWT;
use think\facade\Config;

class AdminTokenService
{
    /**
     * token生成
     * 
     * @param array $admin_user
     * @return string
     */
    public static function create($admin_user = [])
    {
        $data = [
            'admin_user_id' => $admin_user['admin_user_id']
        ];
        $key = Config::get('admin.token.key');
        $iss = Config::get('admin.token.iss');
        $iat = Config::get('admin.token.iat');
        $exp = Config::get('admin.token.exp');

        $payload = [
            'iss'  => $iss,
            'iat'  => $iat,
            'exp'  => $exp,
            'data' => $data,
        ];

        $token = JWT::encode($payload, $key);

        return $token;
    }

    /**
     * token验证
     *
     * @param string $token
     * @param integer $admin_user_id
     * @return json
     */
    public static function verify($token, $admin_user_id = '')
    {
        try {
            $key = Config::get('admin.token.key');
            $decoded = JWT::decode($token, $key, array('HS256'));
            if (!super_admin($admin_user_id)) {
                $token_admin_user_id = $decoded->data->admin_user_id;
                if ($admin_user_id != $token_admin_user_id) {
                    $error_msg = '账号信息错误，请重新登录';
                    $error_data['desc'] = 'Token：请求头部admin_user_id与登录admin_user_id不一致';
                    return error($error_msg, 401, $error_data);
                }
            }
        } catch (\Exception $e) {
            return error('Token：' . $e->getMessage(), 401);
        }
    }
}
