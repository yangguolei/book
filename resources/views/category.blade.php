@extends('master')

@section('title', '书籍类别')

@section('content')
    <div class="weui_cells_title">选择书籍类别</div>
    <div class="weui_cells weui_cells_split">
        <div class="weui_cell weui_cell_select">
            <div class="weui_cell_bd weui_cell_primary">
                <select class="weui_select" name="category">
                    @foreach($categorys as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="weui_cells weui_cells_access">
        <a class="weui_cell" href="javascript:;">
            <div class="weui_cell_bd weui_cell_primary">
                <p>cell standard</p>
            </div>
            <div class="weui_cell_ft">说明文字</div>
        </a>
        <a class="weui_cell" href="javascript:;">
            <div class="weui_cell_bd weui_cell_primary">
                <p>cell standard</p>
            </div>
            <div class="weui_cell_ft">说明文字</div>
        </a>
    </div>

@endsection

@section('my-js')
<script type="text/javascript">
    //初始化也要更新
    _getGategory();
    //监听变化
    $('.weui_select').change(function (event) {
        //更新类别每次点击改变都更新二级类别小事
        _getGategory();
    });

    function _getGategory() {
        //获取weui_select的option选中项的value值
        var parent_id = $('.weui_select option:selected').val();
        $.ajax({
            //提交类型GET
            type: "GET",
            url: '/service/category/parent_id/'+parent_id,//路由地址  给路由传递一个参数parent_id
            dataType: 'json',//参数数据类型
            cache: false,//不缓存
            success: function(data) {
                if(data == null) {
                    $('.bk_toptips').show();
                    $('.bk_toptips span').html('服务端错误');
                    setTimeout(function() {$('.bk_toptips').hide();}, 2000);
                    return;
                }
                $('.weui_cells_access').html('');//清空列表
                if(data.status != 0) {
                    $('.bk_toptips').show();
                    $('.bk_toptips span').html(data.message);
                    setTimeout(function() {$('.bk_toptips').hide();}, 2000);
                    return;
                }
                for(var i = 0;i<data.categorys.length;i++)
                {
                    var next = '/product/category_id/'+data.categorys[i].id;
                    var node='<a class="weui_cell" href="'+next+'">'+
                                  '<div class="weui_cell_bd weui_cell_primary">'+
                                  '<p>'+data.categorys[i].name+'</p>'+
                                  '</div>'+
                                  '<div class="weui_cell_ft"></div>'+
                              '</a>';
                    //追加列表 否则会覆盖
                    $('.weui_cells_access').append(node);

                }


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
