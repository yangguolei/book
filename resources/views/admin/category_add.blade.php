@extends('admin.master')

@section('content')
<form action="" method="post" class="form form-horizontal" id="form-category-add">
  {{ csrf_field() }}{{--form表单的话要添加token--}}
  <div class="row cl">
    <label class="form-label col-3"><span class="c-red">*</span>名称：</label>
    <div class="formControls col-5">
      <input type="text" class="input-text" value="" placeholder="" name="name" datatype="*" nullmsg="名称不能为空">
    </div>
    <div class="col-4"> </div>
  </div>
  <div class="row cl">
    <label class="form-label col-3"><span class="c-red">*</span>序号：</label>
    <div class="formControls col-5">
      <input type="number" class="input-text" value="0" placeholder="" name="category_no"  datatype="*" nullmsg="序号不能为空">
    </div>
    <div class="col-4"> </div>
  </div>
  <div class="row cl">
    <label class="form-label col-3">父类别：</label>
    <div class="formControls col-5"> <span class="select-box" style="width:150px;">
      <select class="select" name="parent_id" size="1">
        <option value="">无</option>
        @foreach($categories as $category)
          <option value="{{$category->id}}">{{$category->name}}</option>
        @endforeach
      </select>
      </span>
    </div>
  </div>
  <div class="row cl">
    <label class="form-label col-3">预览图：</label>
    <div class="formControls col-5">
      {{--隐藏下面的文件框display: none 再将图片点击事件 绑定到隐藏显示的文件框  可以实现点击图片选择上传文件的效果--}}
      <img id="preview_id" src="/admin/images/icon-add.png" style="border: 1px solid #B8B9B9; width: 100px; height: 100px;" onclick="$('#input_id').click()" />
      {{--uploadImageToServer在admin/js/uploadFile中定义的方法--}}
      <input type="file" name="file" id="input_id" style="display: none;" onchange="return uploadImageToServer('input_id','images', 'preview_id');" />
    </div>
  </div>
  <div class="row cl">
    <div class="col-9 col-offset-3">
      <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
    </div>
  </div>
</form>
@endsection

@section('my-js')
<script type="text/javascript">
  $("#form-category-add").Validform({//表单验证H-UI的验证方法
    tiptype:2,
    callback:function(form){
      // form[0].submit();
      // var index = parent.layer.getFrameIndex(window.name);
      // parent.$('.btn-refresh').click();
      // parent.layer.close(index);
        //jquery.form.js的脚本 提交表单
      $('#form-category-add').ajaxSubmit({
          type: 'POST', // 提交方式 get/post
          url: '/admin/service/category/add', // 需要提交的 url
          dataType: 'json',
          data: {
            name: $('input[name=name]').val(),
            category_no: $('input[name=category_no]').val(),
            parent_id: $('select[name=parent_id] option:selected').val(),
            preview: ($('#preview_id').attr('src')!='/admin/images/icon-add.png'?$('#preview_id').attr('src'):''),
            _token: "{{csrf_token()}}"
          },
          success: function(data) {
            if(data == null) {
                //h-ui自带的提示框 提示信息 icon图标类型 1正确 2失败 弹框停留时间2000=2秒
              layer.msg('服务端错误', {icon:2, time:2000});
              return;
            }
            if(data.status != 0) {
              layer.msg(data.message, {icon:2, time:2000});
              return;
            }

            layer.msg(data.message, {icon:1, time:2000});
            //将父窗口进行刷新
  					parent.location.reload();
          },
          error: function(xhr, status, error) {
            console.log(xhr);
            console.log(status);
            console.log(error);
            layer.msg('ajax error', {icon:2, time:2000});
          },
          beforeSend: function(xhr){
              //ajax的进度提示 对应样式
            layer.load(0, {shade: false});
          },
        });

      //表示不需要再去处理form表单
        return false;
    }
  });
</script>
@endsection
