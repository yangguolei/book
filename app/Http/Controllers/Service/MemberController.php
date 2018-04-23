<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;//要获取参数 导入Request类
use App\Models\M3Result;//自定义返回值结果接口
use App\Entity\Member;//成员模型实体类
use App\Entity\TempPhone;//临时手机验证码类 要判断验证码是否过期
use App\Entity\TempEmail;
use App\Models\M3Email;
use App\Tool\UUID;//生成uuid 通用唯一识别码
use Mail;

class MemberController extends Controller
{
    //成员验证注册接口  邮箱验证发送发送
  public function register(Request $request)
  {
    $email = $request->input('email', '');
    $phone = $request->input('phone', '');
    $password = $request->input('password', '');
    $confirm = $request->input('confirm', '');
    $phone_code = $request->input('phone_code', '');
    $validate_code = $request->input('validate_code', '');

    //实例化结果类型
    $m3_result = new M3Result;

    //验证
    if($email == '' && $phone == '') {
      $m3_result->status = 1;
      $m3_result->message = '手机号或邮箱不能为空';
      return $m3_result->toJson();
    }
    if($password == '' || strlen($password) < 6) {
      $m3_result->status = 2;
      $m3_result->message = '密码不少于6位';
      return $m3_result->toJson();
    }
    if($confirm == '' || strlen($confirm) < 6) {
      $m3_result->status = 3;
      $m3_result->message = '确认密码不少于6位';
      return $m3_result->toJson();
    }
    if($password != $confirm) {
      $m3_result->status = 4;
      $m3_result->message = '两次密码不相同';
      return $m3_result->toJson();
    }

    // 手机号注册
    if($phone != '') {
      if($phone_code == '' || strlen($phone_code) != 6) {
        $m3_result->status = 5;
        $m3_result->message = '手机验证码为6位';
        return $m3_result->toJson();
      }

      //数据库查询 三个参数 第二个参数是比较操作符 不写默认=  这里就是查找 where phone这个字段是否等于=$phone这个变量的值
        //获取号码是否已经存在数据库 first()第一条记录
      $tempPhone = TempPhone::where('phone', $phone)->first();

      if($tempPhone->code == $phone_code) {//验证码是否相等
        if(time() > strtotime($tempPhone->deadline)) {//strtotime转换时间戳 判断当前时间是否超出验证码有效期
          $m3_result->status = 7;
          $m3_result->message = '手机验证码不正确';
          return $m3_result->toJson();
        }
        //插入新纪录  实例化成员模型对象
        $member = new Member();
        $member->phone = $phone;//存入电话
        $member->password = md5('bk' + $password);//密码加密 为了防止别人试出来前缀bk 连接字符串加密
        $member->save();//插入
          //保存结果 状态和信息 通过为0
        $m3_result->status = 0;
        $m3_result->message = '注册成功';
        return $m3_result->toJson();//转换为json输出
      } else {
        $m3_result->status = 7;
        $m3_result->message = '手机验证码不正确';
        return $m3_result->toJson();
      }
    } else {// 邮箱注册
      if($validate_code == '' || strlen($validate_code) != 4) {
        $m3_result->status = 6;
        $m3_result->message = '验证码为4位';
        return $m3_result->toJson();
      }

      //从session会话取出验证码验证信息
      $validate_code_session = $request->session()->get('validate_code', '');
      if($validate_code_session != $validate_code) {
          //不一致返回
        $m3_result->status = 8;
        $m3_result->message = '验证码不正确';
        return $m3_result->toJson();//结束输出结果
      }

      $member = new Member();//会员实例对象 准备插入记录
      $member->email = $email;
      $member->password = md5('bk' + $password);
      $member->save();

      //自定义工具类  静态方法生成UUID
      $uuid = UUID::create();

      //邮件模板类对象 模板属性对应发送邮件的方法
      $m3_email = new M3Email();
      $m3_email->to = $email;//用户注册的邮件地址
      $m3_email->cc = 'other_ygl@aliyun.com';//曹松
      $m3_email->subject = '凯恩书店验证';
      $m3_email->content = '请于24小时点击该链接完成验证. http://book.yang.com/service/validate_email'
                        . '?member_id=' . $member->id
                        . '&code=' . $uuid;//URL传递参数 就是URL ?member_id=xxx&code=xxx
        //验证邮件的原理就是生存一份uuid和用户id 将uuid和用户id存入数据库再给用户一个 准备好的链接点击即可

        //临时邮件表 实体
      $tempEmail = new TempEmail();
      $tempEmail->member_id = $member->id;//存入用户id一个用户对应一张表
      $tempEmail->code = $uuid;//存入uuid
        //将时间戳字符串转换成日期类型存入数据表对象   有效期24小时
      $tempEmail->deadline = date('Y-m-d H-i-s', time() + 24*60*60);
      $tempEmail->save();//插入

      //发送邮件 第一个参数是一个模板视图  第二个是传递给视图模板参数
      Mail::send('email_register', ['m3_email' => $m3_email], function ($m) use ($m3_email) {
          // $m->from('hello@app.com', 'Your Application');
          $m->to($m3_email->to, '尊敬的用户')//设置收件人和收件人昵称
            ->cc($m3_email->cc)
            ->subject($m3_email->subject);//主题
      });

      $m3_result->status = 0;
      $m3_result->message = '注册成功！';
      return $m3_result->toJson();
    }
  }

  //登陆检测
  public function login(Request $request)
  {
      $username = $request->get('username','');
      $password = $request->get('password','');
      $validate_code = $request->get('validate_code','');

      //返回结果模板实例化
      $m3_result = new M3Result();
      //校验

      //判断
      //判断验证码的值和session保存的是否一致
      $validate_code_session = $request->session()->get('validate_code');
      if($validate_code!=$validate_code_session)
      {
          //保存结果
          $m3_result->status=1;
          $m3_result->message='验证码错误！';
          //返回 结果
          return $m3_result->toJson();
      }

      $member=null;//定义用户字段 存储读取的记录
      //查询数据库
      if(strpos($username,'@'==true))
      {
          $member = Member::where('email',$username)->first();
      }else
      {
          $member = Member::where('phone',$username)->first();
      }

      if($member==null)
      {
          $m3_result->status=2;
          $m3_result->message='该用户不存在！';
          return $m3_result->toJson();
      }else
      {
          //因为数据库的密码是经过这加密 所以比较需要将密码加密 对比
          if(md5('bk' + $password) !=$member->password)
          {
              $m3_result->status=3;
              $m3_result->message='密码错误！';
              return $m3_result->toJson();
          }
      }

      //保存用户信息
      $request->session()->put('member',$member);

      $m3_result->status=0;
      $m3_result->message='登录成功！';
      return $m3_result->toJson();
      //响应登陆交给客户端~~

  }
}
