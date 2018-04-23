<?php

namespace App\Http\Controllers\Service;
//导入 验证码生成类
use App\Tool\Validate\ValidateCode;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Tool\SMS\SendTemplateSMS;//引入短信验证码
use App\Entity\TempPhone;
use App\Models\M3Result;
use App\Entity\TempEmail;
use App\Entity\Member;

class ValidateController extends Controller
{
  public function create(Request $request)
  {
      //实例化验证码生成类对象
    $validateCode = new ValidateCode;
    //在session会话存入 验证码
    $request->session()->put('validate_code', $validateCode->getCode());
    //返回生成的验证码
    return $validateCode->doimg();
  }

  //短信验证  Request $request获取传递的参数 Request表示类型方便理解 也可以不写
  public function sendSMS(Request $request)
  {
      //实例化结果类型
    $m3_result = new M3Result;

    //获取手机号  默认值空
    $phone = $request->input('phone', '');
    if($phone == '') {
        //未通过记录状态和返回信息  通常0为通过
      $m3_result->status = 1;
      $m3_result->message = '手机号不能为空';
        //因为这是我们自制的 laravel框架不会识别 所以要把他转换成json字符串
      return $m3_result->toJson();
    }
    if(strlen($phone) != 11 || $phone[0] != '1') {
        //未通过记录状态和返回信息  通常0为通过
      $m3_result->status = 2;
      $m3_result->message = '手机格式不正确';
      return $m3_result->toJson();
    }

    //生成验证码
    $sendTemplateSMS = new SendTemplateSMS;
    $code = '';//先声明code 在循环中声明的变量作用域仅在循环
    $charset = '1234567890';//随机字符串
    $_len = strlen($charset) - 1;
    for ($i = 0;$i < 6;++$i) {
        $code .= $charset[mt_rand(0, $_len)];//链接随机验证码
    }
    //发送短信服务  13386649560  接收发送短信方法的返回状态
    $m3_result = $sendTemplateSMS->sendTemplateSMS($phone, array($code, 60), 1);
    //判断返回结果 状态为0通过再进行数据库操作
    if($m3_result->status == 0) {
        //获取模型中的这个号码的 first()取出第一行记录
      $tempPhone = TempPhone::where('phone', $phone)->first();
      if($tempPhone == null) {
          //数据库没有记录 就建立模型对象插入记录 save()方法会自动判断插入还是更新
        $tempPhone = new TempPhone;
      }
      $tempPhone->phone = $phone;
      $tempPhone->code = $code;
      //time() + 60*60获取时间再增加60分钟  然后转换时间戳为相应时间格式存储到数据库   年月日Y-m-d时分秒H-i-s
      $tempPhone->deadline = date('Y-m-d H-i-s', time() + 60*60);
      //存储 会自动判断 更新还是插入
      $tempPhone->save();
    }
      //因为这是我们自制的 laravel框架不会识别 所以要把他转换成json字符串
    return $m3_result->toJson();
  }

  //验证邮箱  通过激活邮箱
  public function validateEmail(Request $request)
  {
    $member_id = $request->input('member_id', '');
    $code = $request->input('code', '');
    //参数不能为空
    if($member_id == '' || $code == '') {
      return '验证异常';
    }

    //根据用户id获取临时邮件记录
    $tempEmail = TempEmail::where('member_id', $member_id)->first();
    if($tempEmail == null) {
      return '验证异常';
    }

    //uuid是否相等
    if($tempEmail->code == $code) {
        //不能超出有效期
      if(time() > strtotime($tempEmail->deadline)) {
        return '该链接已失效';
      }

      //查询到Member实体类中获取记录
      $member = Member::find($member_id);
      $member->active = 1;//设置激活
      $member->save();//更新

        //重定向
      return redirect('/login');
    } else {//uuid初五
      return '该链接已失效';
    }
  }

  //测试短信功能
  public function testPhone()
  {
      $sendTemplateSMS = new SendTemplateSMS;
      $code = '';//先声明code 在循环中声明的变量作用域仅在循环
      $charset = '1234567890';//随机字符串
      $_len = strlen($charset) - 1;
      for ($i = 0;$i < 6;++$i) {
          $code .= $charset[mt_rand(0, $_len)];//链接随机验证码
      }
      //发送短信服务  13386649560
      $m3_result = $sendTemplateSMS->sendTemplateSMS('13386649560', array($code, 60), 1);
      //因为这是我们自制的 laravel框架不会识别 所以要把他转换成json字符串
      dd($m3_result);

  }
}
