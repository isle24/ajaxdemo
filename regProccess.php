<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type:text/html;charset=utf-8");
//禁用缓存,是为了数据一样的前提下还能正常提交，而不是缓存数据
header("Cache-Control:no-cache");
include('myFunc.php');  //包含我的函数库
$username = isset($_POST['username'])?$_POST['username']:'';  //获取用户名
$passw =isset($_POST['passw'])?urlencode($_POST['passw']):'';        //获取密码
$repassw = isset($_POST['repassw'])?urlencode($_POST['repassw']):'';  //获取确认密码
$email = isset($_POST['email'])?$_POST['email']:'';  //获取邮箱
$info='[';          //存放返回页面的数据
$isSucceed = 0;  //判断注册是否成功,如果最后结果为4，则意味着全部正确，注册成功
//1.验证用户名是否非法
$res1 = verifyUser($username);
if($res1[1])
    $info.='{"username":"请输入用户名","state":"false"}';
else if($res1[0])
    $info.='{"username":"用户名非法","state":"false"}';
else if($res1[2])
    $info.='{"username":"用户名已存在","state":"false"}';
else
{
    $info.='{"username":"用户名可用","state":"true"}';
    ++$isSucceed;
}
$info.=',';
//2.验证密码是否非法和强度
$res2 = verifyPassw($passw);
if($res2 == -1)
    $info.='{"passw":"请输入密码","state":"false"}';
else if($res2 == 0)
    $info.='{"passw":"密码非法","state":"false"}';
else
{
    if($res2 == 1)
    $info.='{"passw":"密码强度较弱","state":"true"}';
    else if($res2 == 2)
    $info.='{"passw":"密码强度中等","state":"true"}';
    else if($res2 == 3)
    $info.='{"passw":"密码强度较强","state":"true"}';
    ++$isSucceed;
}

$info.=',';

//3.确认密码
if(empty($repassw))
    $info.='{"repassw":"请先输入密码","state":"false"}';
else if($passw == $repassw)
{
    $info.='{"repassw":"密码一致","state":"true"}';
    ++$isSucceed;
}
else
    $info.='{"repassw":"密码不一致","state":"false"}';
$info.=',';

//4.验证邮箱
$res3 = verifyEmail($email);
if($res3 == -1)
    $info.='{"email":"请输入邮箱","state":"false"}';
else if($res3 == 0)
    $info.='{"email":"邮箱非法","state":"false"}';
else if($res3 == 1)
    $info.='{"email":"此邮箱已被注册","state":"false"}';
else if($res3 == 2)
{
    $info.='{"email":"此邮箱可用","state":"true"}';
    ++$isSucceed;
}
//保存用户注册信息
if($isSucceed == 4)
    saveRegInfo(array($username,$passw,$email));
echo $info.=']';