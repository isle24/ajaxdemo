<?php
/**
 * @function 验证用户名
 * @param $username 用户名
 * @return 返回一个$res数组，里面包含了错误代码,1:用户名非法，1：没有输入用户名，1：用户名存在
 */
header("Access-Control-Allow-Origin: *");
function verifyUser($username)
{
    $res = array();
    //匹配成功返回匹配次数，0表示没有匹配到,匹配字母、数字、下划线
    if(preg_match("/^\\w{6,16}$/",$username) == 0)
        $res[] = 1;
    else
        $res[] = 0;
    if(empty($username))
        $res[] = 1;
    else
        $res[] = 0;
    if(verifyData($username,'name'))  //验证该用户名是否被注册了
        $res[] = 1;
    else
        $res[] = 0;
    return $res;
}

/**
 * @function 验证密码是否非法和密码强度
 * @param $passw 密码
 * @return $res 密码强度
 */
function verifyPassw($passw)
{

    $reg1 = '/^[0-9]{8,16}$/'; //纯数字
    $reg2 = '/^[a-zA-Z]{8,16}$/';//纯字母
    $reg3 = '/^[a-zA-Z\+]{8,16}$/';//纯字母+
    $reg4 = '/^[0-9a-zA-Z]{8,16}$/';//数字和字母组合
    $reg5 = '/^[0-9a-zA-Z\+]{8,16}$/';//数字、’+‘和字母组合
    $res;
    if(empty($passw))
        $res = -1;
    else if(preg_match($reg1,$passw))
        $res = 1;
    else if(preg_match($reg2,$passw))
        $res = 1;
    else if(preg_match($reg3,$passw))
        $res = 2;
    else if(preg_match($reg4,$passw))
        $res = 2;
    else if(preg_match($reg5,$passw))
        $res = 3;
    else
        $res = 0;  //非法密码
    return $res;
}

/**
 * @function 验证邮箱是否非法和是否已经被注册使用
 * @param $email 邮箱
 * @return $res 错误代码
 */
function verifyEmail($email)
{

    $reg = '/^([\w-*\.*])+@(\w-?)+(\.\w{2,})+$/';
    $res;
    if(empty($email))
        $res = -1;
    else if(verifyData($email,'email'))
        $res = 1;
    else if(preg_match($reg,$email))
        $res = 2;
    else
        $res = 0;  //非法邮箱
    return $res;
}

/**
 * @function 验证data是否已经存在
 * @param $data
 * @param $query
 * @return data存在返回1，否则返回0
 */
function verifyData($data,$query)
{
    //1.连接数据库
    @$db = new MySQLi('ip','username','password','database name');
    if(mysqli_connect_errno())
        die("连接数据库失败");
    //2.验证数据是否存在
    $sql = "select $query from users where $query = '{$data}'";
	$res = $db->query($sql); 

if (!$res) {
    die(sprintf("Error: %s", $db->error));
}

$row = $res->fetch_assoc(); 

    //3.关闭数据库
    $db->close();
    return is_null($row)?0:1;
}

/**
 * @function 保存注册用户信息
 * @param $data 要保存的数据，一个数组
 * @return bool $res 返回true表示信息保存成功，false表示失败
 */

function saveRegInfo($data)
{
    //1.连接数据库
    @$db = new MySQLi('ip','your mysql user','your mysql password','database name');
    if(mysqli_connect_errno())
        die("连接数据库失败");
    //2.插入数据
    $username=$data[0];
    $password=$data[1];
    $email=$data[2];
    $GameSalt=$username.$password;
    $GameSalt = md5($GameSalt);
    $GameSalt = "0x".$GameSalt;
    $ip=$_SERVER['REMOTE_ADDR'];
    $sql="call adduser('$username',$GameSalt , '0', '0', '$ip', '0', '$email', '0', '0', '0',  '0', '$password', '0', '0', '0', '0', $GameSalt)";
    $res = $db->query($sql);
    if (!$res) {
        die(sprintf("Error: %s", $db->error));
    }
    //3.关闭数据库
    $db->close();
    return $res;
}
