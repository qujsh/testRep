<?php

/**
 * 做数值 小数点后尾数截取， 默认两位，不做四舍五入处理
 * @param $number
 * @param int $tail         //小数点 保留几位
 * @param bool $isshow      //是否将小数点后的0做显示，不做省略处理，
 * @param bool $round       //判断是否要四舍五入， round = false 表示截取
 * @return mixed
 */
function numberFormat($number, $tail = 2, $isshow = false, $round = true)
{
    $len = strrpos($number, '.') + $tail + ($tail > 0 ? 1 : 0);     //期望的最后数组长度

    if (!$round || $tail < 0)       //如果 tail值为 负，表示往前 截取操作
    {
        //如果是 非四舍五入，那么直接做数值截取
        $number = substr($number, 0, $len );
    }
    else
    {
        $format = '%.'.$tail.'f';
        $number = sprintf($format, $number);        //sprintf 末尾，自带四舍五入 功能
    }

    if (!$isshow)
    {
        //        return (float)$number;                    //在不知道哪个版本的服务器 和 php下，  76.99  会变为 76.98999999999
        return preg_match('/\./', $number)?rtrim(rtrim($number, '0'), '.'):$number;     //所以，这样，或做 正则匹配，需要注意 100这样的情况
    }
    else
    {
        return str_pad($number, $len, '0', STR_PAD_RIGHT);
    }
}

/**
 * 去除多余的0
 */
function del0($s)
{
    $s = trim(strval($s));
    if (preg_match('#^-?\d+?\.0+$#', $s)) {
        return preg_replace('#^(-?\d+?)\.0+$#','$1',$s);
    }
    if (preg_match('#^-?\d+?\.[0-9]+?0+$#', $s)) {
        return preg_replace('#^(-?\d+\.[0-9]+?)0+$#','$1',$s);
    }
    return $s;
}

/**
 * php的数据运算，一旦遇到小数点数这类的，就会自动 转为一种你看着头痛的内容，所以这边需要做 小数转整数的计算，不容易出错
 */
function testFloatNumberCal()
{
    $a = 360.00;
    $b = 350.98;
    var_export($a - $b);            //9.0199999999999818，疯了啊
    echo $a - $b;                           //9.02
    echo $a - $b < 9.02;                    //true
    echo $a * 100 - $b * 100 < 9.02 * 100;  //false
}

/**
 * 获取所有文件，遍历操作
 * @param $path
 * @return array
 */
function get_all_files( $path ){
    $path=iconv("utf-8","gb2312", $path);       //针对中文问题，windows使用编码 gb2312，编码使用 utf-8，需要转码
    $list = array();
    foreach( glob( $path . '/*') as $item ){
        if( is_dir( $item ) ){                                          //window下，is_dir 不能判断utf-8文件路径
            $item=iconv("gb2312","utf-8", $item);   //需要重新转为 utf-8，因为 传入的数据 为utf-8
            $list = array_merge( $list , get_all_files( $item ) );
        } else{                                                         //这儿应该还有个 . ..这样的问题吧，到时候放到linux上再看吧
            $item=iconv("gb2312","utf-8", $item);
            $list[] = $item;
        }
    }
    return $list;
}



$list = get_all_files('C:\Users\Administrator\Desktop\photo');
var_export($list);


/**
 * post 方式提交数据
 * @param $url
 * @param array|NULL $post
 * @param array $options
 * @return mixed
 * @throws Exception
 */
function curl_post($url, $post = NULL, array $options = array())
{
    $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_POSTFIELDS => is_string($post) ? $post : http_build_query($post)
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if( ! $result = curl_exec($ch))
    {
//        trigger_error(curl_error($ch));

//        Log::WARN("request:" . json_encode(($options + $defaults)));
//        Log::WARN("request:" . curl_error($ch));

        throwErrorObjMessage(Error_Wxa::WX_MESSAGE_NOT_DEFINED, curl_errno($ch) . ' ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
}

/**
 * get 方式提交数据
 * @param $url
 * @param array|NULL $get
 * @param array $options
 * @return mixed
 * @throws Exception
 */
function curl_get($url, array $get = NULL, array $options = array())
{
    $defaults = array(
        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get),
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 5
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if( ! $result = curl_exec($ch))
    {
//        trigger_error(curl_error($ch));

//        Log::WARN("request:" . json_encode(($options + $defaults)));
//        Log::WARN("request:" . curl_error($ch));

        throwErrorObjMessage(Error_Wxa::WX_MESSAGE_NOT_DEFINED, curl_error($ch));
    }
    curl_close($ch);
    return $result;
}

