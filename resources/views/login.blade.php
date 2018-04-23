@extends('master')

@include('component.loading')

@section('title', '登录')

@section('content')
<div class="weui_cells_title"></div>
<div class="weui_cells weui_cells_form">
  <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label">帐号</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="tel" placeholder="邮箱或手机号" name="username"/>
      </div>
  </div>
  <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label">密码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="password" placeholder="不少于6位" name="password"/>
      </div>
  </div>
  <div class="weui_cell weui_vcode">
      <div class="weui_cell_hd"><label class="weui_label">验证码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="text" placeholder="请输入验证码" name="validate_code"/>
      </div>
      <div class="weui_cell_ft">
          <img src="/service/validate_code/create" class="bk_validate_code"/>{{--路由创建验证码--}}
      </div>
  </div>
</div>
<div class="weui_cells_tips"></div>
<div class="weui_btn_area">
  <a class="weui_btn weui_btn_primary" href="javascript:" onclick="onLoginClick();">登录</a>
</div>
<a href="/register" class="bk_bottom_tips bk_important">没有帐号? 去注册</a>
@endsection

@section('my-js')
<script type="text/javascript">

    //绑定元素点击事件
  $('.bk_validate_code').click(function () {
      //防止个别浏览器读取缓存 导致无法更换验证码图片 所以URL附带个随机参数
    $(this).attr('src', '/service/validate_code/create?random=' + Math.random());
  });

  function onLoginClick(){
      //账号
      var username = $('input[name=username]').val();//获取元素值
      if(username.length==0)
      {
          $('.bk_toptips').show();//定义在book.css中的提示框面板  继承自master模板默认隐藏的
          $('.bk_toptips span').html('账号不能为空！');//显示错误内容
          setTimeout(function() {$('.bk_toptips').hide();}, 2000);//指定2秒后隐藏
          return;
      }
      if(username.indexOf('@')==-1)//查询索引 -1表示未找到 手机号
      {
          if(username.length!=11 || username[0]!=1)//开头不为1 或者不是11位
          {
              $('.bk_toptips').show();//定义在book.css中的提示框面板  继承自master模板默认隐藏的
              $('.bk_toptips span').html('账号格式不正确！');//显示错误内容
              setTimeout(function() {$('.bk_toptips').hide();}, 2000);//指定2秒后隐藏
              return;
          }

      }else{
          if(username.indexOf('.')==-1) //邮箱除了要@还有.
          {
              $('.bk_toptips').show();//定义在book.css中的提示框面板  继承自master模板默认隐藏的
              $('.bk_toptips span').html('账号格式不正确!');//显示错误内容
              setTimeout(function() {$('.bk_toptips').hide();}, 2000);//指定2秒后隐藏
              return;
          }
      }
      //密码
      var password = $('input[name=password]').val();
      if(password.length==0)
      {
          $('.bk_toptips').show();//定义在book.css中的提示框面板  继承自master模板默认隐藏的
          $('.bk_toptips span').html('密码不为空!');//显示错误内容
          setTimeout(function() {$('.bk_toptips').hide();}, 2000);//指定2秒后隐藏
          return;
      }
      if(password.length<6)
      {
          $('.bk_toptips').show();//定义在book.css中的提示框面板  继承自master模板默认隐藏的
          $('.bk_toptips span').html('密码不少于6位！');//显示错误内容
          setTimeout(function() {$('.bk_toptips').hide();}, 2000);//指定2秒后隐藏
          return;
      }

      //验证码
      var validate_code = $('input[name=validate_code]').val();//获取验证码
      if(validate_code.length==0)
      {
          $('.bk_toptips').show();//定义在book.css中的提示框面板  继承自master模板默认隐藏的
          $('.bk_toptips span').html('验证码不为空！');//显示错误内容
          setTimeout(function() {$('.bk_toptips').hide();}, 2000);//指定2秒后隐藏
          return;
      }
      if(validate_code.length<0)
      {
          $('.bk_toptips').show();//定义在book.css中的提示框面板  继承自master模板默认隐藏的
          $('.bk_toptips span').html('验证码不少于4位！');//显示错误内容
          setTimeout(function() {$('.bk_toptips').hide();}, 2000);//指定2秒后隐藏
          return;
      }

      $.ajax({
          //提交类型post
          type: "POST",
          url: '/service/login',//路由地址
          dataType: 'json',//参数数据类型
          cache: false,//不缓存
          //访问路由 传递参数 最后一个token防止跨站攻击
          data: {username: username, password: password, validate_code: validate_code, _token: "{{csrf_token()}}"},
          success: function(data) {
              if(data == null) {
                  $('.bk_toptips').show();
                  $('.bk_toptips span').html('服务端错误');
                  setTimeout(function() {$('.bk_toptips').hide();}, 2000);
                  return;
              }
              if(data.status != 0) {
                  $('.bk_toptips').show();
                  $('.bk_toptips span').html(data.message);
                  setTimeout(function() {$('.bk_toptips').hide();}, 2000);
                  return;
              }

              $('.bk_toptips').show();
              $('.bk_toptips span').html('登录成功！');
              setTimeout(function() {$('.bk_toptips').hide();}, 2000);

              //客户端响应跳转窗口 转跳到登陆前的位置方便用户具体操作
              location.href="{{$request_url}}";
          },
          error: function(xhr, status, error) {
              console.log(xhr);
              console.log(status);
              console.log(error);
          }
      });

  }
</script>
@endsection
