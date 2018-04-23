<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;//导入资源命名空间

class MemberController extends Controller
{
  public function toLogin(Request $request)
  {
      //获取跳转登陆前页面信息
     $request_url = $request->input('return_url','');
     //附带参数 用于Ajax回调函数进行跳转      url解码 传递过来的时加密的
    return view('login')->with('request_url',urldecode($request_url));
  }

  public function toRegister($value='')
  {
    return view('register');
  }
}
