<?php


namespace App\Common;


class Validate
{
    public static function number($val, $pattern = [])
    {
        $required = $pattern['required'] ?? false;
        $tail = $pattern['tail'] ?? 2;
        $min = $pattern['min'] ?? 0;
        $max = $pattern['max'] ?? 99999999;

        if ($required && '' === strval($val)) {
            return "不能为空";
        }
        if (!is_numeric($val)) {
            return '请输入数字';
        }
        if ($val < $min) {
            return '不能小于' . $min;
        }
        if ($val > $max) {
            return '不能大于' . $max;
        }

        $arr = explode('.', $val);
        if (!empty($arr[1])) {
            if (strlen($arr[1]) > $tail) {
                return '最多保留小数点后' . $tail . '位';
            }
        }

        return 0;
    }

    public static function string($val, $pattern = [])
    {
        $required = $pattern['required'] ?? false;
        $min_len = $pattern['min_len'] ?? 0;
        $max_len = $pattern['max_len'] ?? 0;

        if ($required && '' === strval($val)) {
            return "不能为空";
        }

        $len = mb_strlen($val, 'utf-8');
        if ($min_len && $min_len > $len) {
            return "最多{$min_len}字符";
        }
        if ($max_len && $max_len < $len) {
            return "最多{$max_len}字符";
        }

        return 0;
    }

    public static function phone($val, $pattern = [])
    {
        $required = $pattern['required'] ?? false;

        if ($required && '' === strval($val)) {
            return "不能为空";
        }
        if ($val && !preg_match('/^1\d{10}$/', $val)) {
            return "不正确";
        }

        return 0;
    }

    //ASCII
    public static function ascii($val, $pattern = [])
    {
        if (strlen($val) !== mb_strlen($val)) {
            return "不正确";
        }
        return 0;
    }

    //是否是严格的 Y-m-d 格式
    public static function date($val, $pattern = [])
    {
        $regx = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
        if (!preg_match($regx, $val, $parts)) {
            return "不正确";
        }
        //检测是否为日期,checkdate 的参数为：月日年
        if (!checkdate($parts[2], $parts[3], $parts[1])) {
            return "不正确";
        }

        return 0;
    }

    //身份证号
    public static function IDCard($id, $pattern = [], &$info = null)
    {
        $id = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        if (!preg_match($regx, $id)) {
            return '不正确';
        }

        $arr_split = array();
        //检查15位
        if (15 == strlen($id)) {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return '不正确';
            }
            $sexint = (int)substr($id, 14, 1);
            $sex = $sexint % 2 === 0 ? '女' : '男';
            $info = array('birth' => $dtm_birth, 'sex' => $sex);
            return 0;
        }

        //检查18位
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) { //检查生日日期是否正确
            return '不正确';
        }
        //检验18位身份证的校验码是否正确。
        //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
        $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $sign = 0;
        for ($i = 0; $i < 17; $i++) {
            $b = (int)$id[$i];
            $w = $arr_int[$i];
            $sign += $b * $w;
        }
        $n = $sign % 11;
        $val_num = $arr_ch[$n];
        if ($val_num != substr($id, 17, 1)) {
            // 身份证的校验码不正确
            return '不正确';
        }
        $sexInt = (int)substr($id, 16, 1);
        $sex = $sexInt % 2 === 0 ? '女' : '男';
        $info = array('birth' => $dtm_birth, 'sex' => $sex);
        return 0;
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $validated
     * @param bool $deal_null
     * @return mixed
     */
    public static function work(array $data, array $rules, &$validated = null, $deal_null = false)
    {
        $validator = new self();
        foreach ($rules as $item) {
            $value = $data[$item['field']] ?? null;
            $type = $item['type'] ?? 'string';
            $errMsg = $validator->$type($value, $item);
            if ($errMsg) {
                $label = $item['label'] ?? '';
                return $label . $errMsg;
            }
            $validated[$item['field']] = $value;
        }
        return 0;
    }
}
