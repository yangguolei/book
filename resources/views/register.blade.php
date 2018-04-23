@extends('master')

@section('title', '注册')

@section('content')
<div class="weui_cells_title">注册方式</div>
<div class="weui_cells weui_cells_radio">
  <label class="weui_cell weui_check_label" for="x11">
      <div class="weui_cell_bd weui_cell_primary">
          <p>手机号注册</p>
      </div>
      <div class="weui_cell_ft">
          <input type="radio" class="weui_check" name="register_type" id="x11" checked="checked">
          <span class="weui_icon_checked"></span>
      </div>
  </label>
  <label class="weui_cell weui_check_label" for="x12">
      <div class="weui_cell_bd weui_cell_primary">
          <p>邮箱注册</p>
      </div>
      <div class="weui_cell_ft">
          <input type="radio" class="weui_check" name="register_type" id="x12">
          <span class="weui_icon_checked"></span>
      </div>
  </label>
</div>
<div class="weui_cells weui_cells_form">
  <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label">手机号</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="text" placeholder="" name="phone"/>
      </div>
  </div>
  <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label">密码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="password" placeholder="不少于6位" name='passwd_phone'/>
      </div>
  </div>
  <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label">确认密码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="password" placeholder="不少于6位" name='passwd_phone_cfm'/>
      </div>
  </div>
  <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label">短信验证码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="text" placeholder="" name='phone_code'/>
      </div>
      <p class="bk_important bk_phone_code_send">发送验证码</p>
      <div class="weui_cell_ft">
      </div>
  </div>
</div>
<div class="weui_cells weui_cells_form" style="display: none;">
  <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label">邮箱</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="text" placeholder="" name='email'/>
      </div>
  </div>
  <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label">密码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="password" placeholder="不少于6位" name='passwd_email'>
      </div>
  </div>
  <div class="weui_cell">
      <div class="weui_cell_hd"><label class="weui_label">确认密码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="password" placeholder="不少于6位" name='passwd_email_cfm'/>
      </div>
  </div>
  <div class="weui_cell weui_vcode">
      <div class="weui_cell_hd"><label class="weui_label">验证码</label></div>
      <div class="weui_cell_bd weui_cell_primary">
          <input class="weui_input" type="text" placeholder="请输入验证码" name='validate_code'/>
      </div>
      <div class="weui_cell_ft">
          <img src="/service/validate_code/create" class="bk_validate_code"/>
      </div>
  </div>
</div>
<div class="weui_cells_tips"></div>
<div class="weui_btn_area">
  <a class="weui_btn weui_btn_primary" href="javascript:" onclick="onRegisterClick();">注册</a>
</div>
<a href="/login" class="bk_bottom_tips bk_important">已有帐号? 去登录</a>{{--指定路由跳转到登陆--}}
@endsection

@section('my-js')
<script type="text/javascript">
  //设置标题
    //$('.bk_title_content').html("注册"); 在模板基类统一变化
  //根据点击控制邮箱注册和手机号注册的页面切换
  $('#x12').next().hide();
  $('input:radio[name=register_type]').click(function(event) {
    $('input:radio[name=register_type]').attr('checked', false);
    $(this).attr('checked', true);
    if($(this).attr('id') == 'x11') {
      $('#x11').next().show();
      $('#x12').next().hide();
      $('.weui_cells_form').eq(0).show();
      $('.weui_cells_form').eq(1).hide();
    } else if($(this).attr('id') == 'x12') {
      $('#x12').next().show();
      $('#x11').next().hide();
      $('.weui_cells_form').eq(1).show();
      $('.weui_cells_form').eq(0).hide();
    }
  });

  //点击验证码元素时生成新的验证码
  $('.bk_validate_code').click(function () {
    $(this).attr('src', '/service/validate_code/create?random=' + Math.random());
  });

</script>
<script type="text/javascript">
  //发送验证码逻辑
  var enable = true;//用于判断是否允许发送短信
  $('.bk_phone_code_send').click(function(event) {
    if(enable == false) {
      return;//不允许发送直接返回
    }
//验证
      //获取手机号输入框的值 input[name=phone]获取name为phone的输入框值
    var phone = $('input[name=phone]').val();
    // 手机号不为空
    if(phone == '') {
      $('.bk_toptips').show();//定义在book.css中的提示框面板  继承自master模板默认隐藏的
      $('.bk_toptips span').html('请输入手机号');//显示错误内容
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);//指定2秒后隐藏
      return;
    }
    // 手机号格式
    if(phone.length != 11 || phone[0] != '1') {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('手机格式不正确');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return;
    }

    //移除当前字体响应按下事件  在book.css文件中定义的样式
      //调用的变量和属性都要用jqery包起来$()
    $(this).removeClass('bk_important');
    //替换新字体
    $(this).addClass('bk_summary');
    //只要点击了通过了验证就设置为false
    enable = false;
    var num = 60;//等待重新发送验证码计时
      //计时器 第一个参数是逻辑 第二个参数是时间 1000就是1秒
    var interval = window.setInterval(function() {
        //更新按钮元素提示
      $('.bk_phone_code_send').html(--num + 's 重新发送');
      //判断是否倒计时结束
      if(num == 0) {
          //字体样式更换回来
        $('.bk_phone_code_send').removeClass('bk_summary');
        $('.bk_phone_code_send').addClass('bk_important');
        enable = true;//再次开启点击
        window.clearInterval(interval);//即使结束计时器销毁
          //更新按钮元素提示
        $('.bk_phone_code_send').html('重新发送');
      }
    }, 1000);

    //手机验证码
    $.ajax({
        type:"POST",
        //利用ajax访问路由  发送验证码
      url: '/service/validate_phone/send',
        //指定返回数据类型以json返回
      dataType: 'json',
      cache: false,//不缓存  Post请求必须传递_token
      data: {phone: phone,_token: "{{csrf_token()}}"},//传递给发送短信方法的参数 phone是在jqury中定义的变量
        //调用成功失败后的回调方法
      success: function(data) {
        if(data == null) {//如果为空说明服务端出错 也就是短信平台的信息
          $('.bk_toptips').show();//提示框显示
          $('.bk_toptips span').html('服务端错误');
          setTimeout(function() {$('.bk_toptips').hide();}, 2000);
          return;
        }
        if(data.status != 0) {//不为表示达到服务器都是出现其他错误 俺么提示错误
          $('.bk_toptips').show();
          $('.bk_toptips span').html(data.message);
          setTimeout(function() {$('.bk_toptips').hide();}, 2000);
          return;
        }
        $('.bk_toptips').show();
        $('.bk_toptips span').html('发送成功!');
        setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      },
      error: function(xhr, status, error) {
          //返回错误信息方便调试
        console.log(xhr);
        console.log(status);
        console.log(error);
      }
    });
  });
</script>
<script type="text/javascript">

  //注册按钮事件
  function onRegisterClick() {

    $('input:radio[name=register_type]').each(function(index, el) {
      if($(this).attr('checked') == 'checked') {
        var email = '';
        var phone = '';
        var password = '';
        var confirm = '';
        var phone_code = '';
        var validate_code = '';

        //选中手机那么获取校验
        var id = $(this).attr('id');
        if(id == 'x11') {
          phone = $('input[name=phone]').val();
          password = $('input[name=passwd_phone]').val();
          confirm = $('input[name=passwd_phone_cfm]').val();
          phone_code = $('input[name=phone_code]').val();
          if(verifyPhone(phone, password, confirm, phone_code) == false) {
            return;
          }
        } else if(id == 'x12') {//选中邮箱那么获取邮箱信息校验
          email = $('input[name=email]').val();
          password = $('input[name=passwd_email]').val();
          confirm = $('input[name=passwd_email_cfm]').val();
          validate_code = $('input[name=validate_code]').val();
          if(verifyEmail(email, password, confirm, validate_code) == false) {
            return;
          }
        }

        $.ajax({
            //提交类型post
          type: "POST",
          url: '/service/register',//路由地址
          dataType: 'json',//参数数据类型
          cache: false,//不缓存
          data: {phone: phone, email: email, password: password, confirm: confirm,
            phone_code: phone_code, validate_code: validate_code, _token: "{{csrf_token()}}"},
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
            $('.bk_toptips span').html('注册成功!');
            setTimeout(function() {$('.bk_toptips').hide();}, 2000);
              //客户端响应跳转窗口 转跳地址
              location.href="/login";
          },
          error: function(xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
          }
        });
      }
    });
  }

  //手机验证方法
  function verifyPhone(phone, password, confirm, phone_code) {
    // 手机号不为空
    if(phone == '') {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('请输入手机号');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    // 手机号格式
    if(phone.length != 11 || phone[0] != '1') {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('手机格式不正确');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(password == '' || confirm == '') {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('密码不能为空');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(password.length < 6 || confirm.length < 6) {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('密码不能少于6位');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(password != confirm) {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('两次密码不相同!');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(phone_code == '') {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('手机验证码不能为空!');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(phone_code.length != 6) {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('手机验证码为6位!');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    return true;
  }

  //邮箱验证方法
  function verifyEmail(email, password, confirm, validate_code) {
    // 邮箱不为空
    if(email == '') {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('请输入邮箱');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    // 邮箱格式
    if(email.indexOf('@') == -1 || email.indexOf('.') == -1) {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('邮箱格式不正确');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(password == '' || confirm == '') {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('密码不能为空');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(password.length < 6 || confirm.length < 6) {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('密码不能少于6位');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(password != confirm) {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('两次密码不相同!');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(validate_code == '') {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('验证码不能为空!');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    if(validate_code.length != 4) {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('验证码为4位!');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);
      return false;
    }
    return true;
  }

</script>

@endsection
