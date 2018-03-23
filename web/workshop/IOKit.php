<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2018/3/23
 * Time: 10:51
 */

namespace sinri\Verbum\workshop;


class IOKit
{
    /**
     * @param string $name
     * @param null $default
     * @return string|mixed
     */
    public static function read($name,$default=null){
        $body=file_get_contents("php://input");
        $req=json_decode($body,true);
        if(!$req)$req=$_REQUEST;
        if(isset($req[$name]))return $req[$name];
        return $default;
    }

    /**
     * @param mixed $x
     * @return bool
     */
    public static function isValidString($x){
        return is_string($x) && strlen($x)>0;
    }

    public static function sayOK($data){
        echo json_encode(['code'=>'OK','data'=>$data]);
    }

    public static function sayFail($error){
        echo json_encode(['code'=>'FAIL','data'=>$error]);
    }
}