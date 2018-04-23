@extends('master')

@section('title', '购物车')

@section('content')
    {{--购物车详情--}}
    <div class="page bk_content" style="top: 0;">
        <div class="weui_cells weui_cells_checkbox">
            @foreach($cart_items as $cart_item)
                {{--这个标签属性for必须与下面 input的checkbox的id属性相同否则无法被选中 weui是这样的--}}
                <label class="weui_cell weui_check_label" for="{{$cart_item->product->id}}">
                    <div class="weui_cell_hd" style="width: 23px;">
                        <input type="checkbox" class="weui_check" name="cart_item" id="{{$cart_item->product->id}}" checked="checked">
                        <i class="weui_icon_checked"></i>
                    </div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <div style="position: relative;">
                            <img class="bk_preview" src="{{$cart_item->product->preview}}" class="m3_preview" onclick="_toProduct({{$cart_item->product->id}});"/>
                            <div style="position: absolute; left: 100px; right: 0; top: 0">
                                <p>{{$cart_item->product->name}}</p>
                                <p class="bk_time" style="margin-top: 15px;">数量: <span class="bk_summary">x{{$cart_item->count}}</span></p>
                                <p class="bk_time">总计: <span class="bk_price">￥{{$cart_item->product->price * $cart_item->count}}</span></p>
                            </div>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>
    </div>
    {{--引入表单 提交Post请求--}}
    <form action="/order_commit" id="order_commit" method="post">
        {{ csrf_field() }}
        <input type="hide" name="product_ids" value="" />
        <input type="hide" name="is_wx" value="" />
    </form>
    {{--底部按钮--}}
    <div class="bk_fix_bottom">
        <div class="bk_half_area">
            <button class="weui_btn weui_btn_primary" onclick="_toCharge();">结算</button>
        </div>
        <div class="bk_half_area">
            <button class="weui_btn weui_btn_default" onclick="_onDelete();">删除</button>
        </div>
    </div>

@endsection

@section('my-js')
    <script type="text/javascript">
        //监听控制变化
        $('input:checkbox[name=cart_item]').click(function (event) {
            var checked = $(this).attr('checked')
            if (checked == 'checked') {
                $(this).attr('checked', false);
                $(this).next().removeClass('weui_icon_checked');
                $(this).next().addClass('weui_icon_unchecked');
            } else
            {
                $(this).attr('checked', true);
                $(this).next().removeClass('weui_icon_unchecked');
                $(this).next().addClass('weui_icon_checked');
            }
        });
        function _onDelete() {
            var product_ids_arr = [];
            $('input:checkbox[name=cart_item]').each(function(index, el) {
                if($(this).attr('checked') == 'checked') {
                    product_ids_arr.push($(this).attr('id'));
                }
            });

            if(product_ids_arr.length == 0) {
                $('.bk_toptips').show();
                $('.bk_toptips span').html('请选择删除项');
                setTimeout(function() {$('.bk_toptips').hide();}, 2000);
                return;
            }

            $.ajax({
                type: "GET",
                url: '/service/cart/delete',
                dataType: 'json',
                cache: false,
                data: {product_ids: product_ids_arr+''},
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

                    //刷新界面
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }
        //结算方法
        function _toCharge()
        {
            var product_ids_arr='';

            var product_ids_arr = [];
            $('input:checkbox[name=cart_item]').each(function(index, el) {
                if($(this).attr('checked') == 'checked') {
                    product_ids_arr.push($(this).attr('id'));
                }
            });

            if(product_ids_arr.length == 0) {
                $('.bk_toptips').show();
                $('.bk_toptips span').html('请选择提交项');
                setTimeout(function() {$('.bk_toptips').hide();}, 2000);
                return;
            }

            //cart_item_arr选中项  提交订单需要经过中间件判断是否登陆 所以相应路由要配置中间件或者写入中间件组中
            //location.href='/order_pay?cart_item_ids='+cart_item_arr;
            //因为这里会被拦截器链接 所以尽量不要提交参数(就是？xxx=xxx这种) 或者把参数放在URL中
            //location.href='/order_commit/'+product_ids_arr;

            $('input[name=product_ids]').val(product_ids_arr+'');
            // $('input[name=is_wx]').val(is_wx+'');
            $('#order_commit').submit();//提交表单

        }


    </script>
@endsection
