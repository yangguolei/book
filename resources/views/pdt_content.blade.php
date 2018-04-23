@extends('master')

@section('title', $product->name)

@section('content')

    <link rel="stylesheet" href="/css/swipe.css">
    <div class="page bk_content" style="top: 0;">
        {{--图片
        <div> style="top: 0;紧贴顶部 不然又一条空隙
            @foreach($pdt_images as $pdt_image)
                <div>
                    <img class="img-responsive " src="{{$pdt_image->image_path}}"/>
                </div>
            @endforeach
        </div>
        --}}
        <div class="addWrap">
            <div class="swipe" id="mySwipe">
                <div class="swipe-wrap">
                    @foreach($pdt_images as $pdt_image)
                        <div>
                            <img class="img-responsive " src="{{$pdt_image->image_path}}"/>
                        </div>
                    @endforeach
                </div>
            </div>
            <ul id="position">
                @foreach($pdt_images as $index => $pdt_image)
                    <li class={{$index == 0 ? 'cur' : ''}}></li>
                @endforeach
            </ul>
        </div>
        {{--标题价格以及详情--}}
        <div class="weui_cells_title">
            <span class="bk_title">{{$product->name}}</span>
            <span class="bk_price">￥{{$product->price}}</span>
        </div>
        <div class="weui_cells">
            <div class="weui_cell">
            <p class="bk_summary">{{$product->summary}}</p>
            </div>
        </div>
        <div class="weui_cells_title">详细介绍</div>
        <div class="weui_cells">
            <div class="weui_cell">
                @if($pdt_content!=null) {{--防止为空报错--}}
                    {!! $pdt_content->content !!}
                    @else
                    @endif
            </div>
        </div>
        <div class="bk_fix_bottom">
            <div class="bk_half_area">
                <button class="weui_btn weui_btn_primary" onclick="_addCart();">加入购物车</button>
            </div>
            <div class="bk_half_area">
                <button class="weui_btn weui_btn_default" onclick="_toCart();">查看购物车(<span id="cart_num" class="m3_price">{{$count}}</span>)</button>
            </div>
        </div>
    </div>


@endsection

@section('my-js')
    <script type="/js/swipe.min.js" charset="utf-8"></script>
    <script type="text/javascript">
    var bullets = document.getElementById('position').getElementsByTagName('li');
    Swipe(document.getElementById('mySwipe'), {
        auto: 5000,
        continuous: true,
        disableScroll: false,
        callback: function(pos) {
            var i = bullets.length;
            while (i--) {
                bullets[i].className = '';
            }
            bullets[pos].className = 'cur';
        }
    });

        function _addCart() {
            //定义变量 获取本视图的$product的id属性  $product是上一个视图传递的数值
            var product_id = "{{$product->id}}";
            $.ajax({
                type: "GET",//请求类型
                url: '/service/cart/add/' + product_id,//提交路由 增加购物车数量
                dataType: 'json',
                cache: false,
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

                    //获取id为cart_num的元素并且获取到值
                    var num = $('#cart_num').html();
                    if(num == '') num = 0;//没有就默认为0
                    $('#cart_num').html(Number(num) + 1);//赋值 Number(num)类型转换

                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }

        //跳转购物车
        function _toCart()
        {
            location.href='/cart';//跳转路由
        }
    </script>
@endsection
