<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Entity\Category;
use App\Models\M3Email;
use App\Models\M3Result;

class BookController extends Controller
{
  public function getCategoryByParentId($parent_id)
  {
      //获取parent_id为空的记录 也就是一级菜单
      $categorys = Category::where('parent_id',$parent_id)->get();
      //定义返回值 因为都是用这个接口写的返回值 所以要保持一致
      $m3_result = new M3Result();
      $m3_result->status=0;
      $m3_result->message='返回成功';
      //额外定义一个变量 返回类别记录 这是php的特性但是少用效率不高
      $m3_result->categorys = $categorys;

      //自己定义类就要转换成json 这个转换方法也是自己写的
      return $m3_result->toJson();


      //laravel查询出来的数据结果可以直接显示 会自动转换成json传输
      //return $categorys;//接口不用显示视图 所以直接返回参数
  }
}
