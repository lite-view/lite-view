<?php


namespace App\Common;


class JWT
{
    /**
     * @param array $data
     * @param int $ttl token有效期，单位分钟
     * @param string $guard
     * @return mixed
     */
    public static function create(array $data, $ttl = 1, $guard = 'api')
    {
        $data['ttl'] = $ttl;
        $data['guard'] = $guard;
        $data['timestamp'] = time();
        $data['nonce'] = substr(str_shuffle('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890'), 0, 6);
        $data['sign'] = self::sign($data);
        return base64_encode(json_encode($data));
    }

    public static function auth($token, $guard, &$info = null)
    {
        if (empty($token)) {
            return 1;
        }
        $info = json_decode(base64_decode($token), true);
        $sign = $info['sign'] ?? '';
        unset($info['sign']);

        //验证
        if ($sign !== self::sign($info)) {
            return 2;//签名错误
        }
        if (time() > $info['ttl'] * 60 + $info['timestamp']) {
            return 3;//过期
        }
        if ($info['guard'] != $guard) {
            return 4;//guard 不匹配
        }
        return 0;
    }

    private static function sign($data)
    {
        ksort($data);
        $string1 = http_build_query($data) . cfg('jwt_secret');
        return md5($string1);
    }

    public static function passwdMake($passwd)
    {
        $md5Key = cfg('app_key');
        return md5(md5($passwd) . $md5Key);
    }

    public static function passwdAuth($value, $hashedValue)
    {
        return self::passwdMake($value) === $hashedValue;
    }
}