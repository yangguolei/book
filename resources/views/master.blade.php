<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
  <title>@yield('title')</title>
  <link rel="stylesheet" href="/css/weui.css">
  <link rel="stylesheet" href="/css/book.css">
</head>
<body>
<div class="bk_title_bar">
    <img class="bk_back" src="/images/back.png" alt="back" onclick="history.go(-1);">{{--返回键 history.go(-1);返回上一页--}}
    <p class="bk_title_content"></p>{{--标题--}}
    <img class="bk_menu" src="/images/menu.png" alt="menu" onclick="onMenuClick();">{{--菜单键--}}
</div>
<div class="page">{{--weui通常将内容存入page的class中--}}
  @yield('content')
</div>

<!-- tooltips -->{{--弹出框样式 默认隐藏 方便继承的模板使用--}}
<div class="bk_toptips"><span></span></div>
<!--BEGIN actionSheet-->
<div id="actionSheet_wrap">
    <div class="weui_mask_transition" id="mask"></div>
    <div class="weui_actionsheet" id="weui_actionsheet">
        <div class="weui_actionsheet_menu">
            <div class="weui_actionsheet_cell" onclick="onMenuItemClick(1)">主页</div>
            <div class="weui_actionsheet_cell" onclick="onMenuItemClick(2)">书籍类别</div>
            <div class="weui_actionsheet_cell" onclick="onMenuItemClick(3)">购物车</div>
            <div class="weui_actionsheet_cell" onclick="onMenuItemClick(4)">我的订单</div>
        </div>
        <div class="weui_actionsheet_action">
            <div class="weui_actionsheet_cell" id="actionsheet_cancel">取消</div>
        </div>
    </div>
</div>

</body>
<script src="/js/book.min.js" charset="utf-8"></script>
<script src="/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript">
function hideActionSheet(weuiActionsheet, mask) {
    weuiActionsheet.removeClass('weui_actionsheet_toggle');
    mask.removeClass('weui_fade_toggle');
    weuiActionsheet.on('transitionend', function () {
        mask.hide();
    }).on('webkitTransitionEnd', function () {
        mask.hide();
    })
}

function onMenuClick () {
    var mask = $('#mask');
    var weuiActionsheet = $('#weui_actionsheet');
    weuiActionsheet.addClass('weui_actionsheet_toggle');
    mask.show().addClass('weui_fade_toggle').click(function () {
        hideActionSheet(weuiActionsheet, mask);
    });
    $('#actionsheet_cancel').click(function () {
        hideActionSheet(weuiActionsheet, mask);
    });
    weuiActionsheet.unbind('transitionend').unbind('webkitTransitionEnd');
}

function onMenuItemClick(index) {
  var mask = $('#mask');
  var weuiActionsheet = $('#weui_actionsheet');
  hideActionSheet(weuiActionsheet, mask);
  if(index == 1) {
      $('.bk_toptips').show();
      $('.bk_toptips span').html('敬请期待');
      setTimeout(function() {$('.bk_toptips').hide();}, 2000);

  } else if(index == 2) {
      location.href='/category';
  } else if(index == 3){
      location.href='/cart';
  }else if(index == 4){
      location.href='/order_list';
  }
  else {
    $('.bk_toptips').show();
    $('.bk_toptips span').html("敬请期待!");
    setTimeout(function() {$('.bk_toptips').hide();}, 2000);
  }
}

//设置标题 绑定标题那个变量 继承重写了里也会变化  将标题栏和标题保持一致
$('.bk_title_content').html(document.title);
</script>

@yield('my-js')
</html>
