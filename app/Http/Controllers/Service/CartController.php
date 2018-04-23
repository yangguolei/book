<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\M3Result;
use App\Entity\CartItem;

class CartController extends Controller
{
    //添加购物车
  public function addCart(Request $request,$product_id)
  {
      //返回值结果类
      $m3_result=new M3Result();
      $m3_result->status= 0;
      $m3_result->message='添加成功';
      // 如果当前已经登录
      $member = $request->session()->get('member', '');
      if($member != '') {
          $cart_items = CartItem::where('member_id', $member->id)->get();

          $exist = false;
          foreach ($cart_items as $cart_item) {
              if ($cart_item->product_id == $product_id) {
                  $cart_item->count++;
                  $cart_item->save();
                  $exist = true;
                  break;
              }
          }

          if ($exist == false) {
              $cart_item = new CartItem;
              $cart_item->product_id = $product_id;
              $cart_item->count = 1;
              $cart_item->member_id = $member->id;
              $cart_item->save();
          }
          return $m3_result->toJson();
      }
      $bk_cart = $request->cookie('bk_cart');
      $bk_cart_arr = ($bk_cart!=null ? explode(',', $bk_cart) : array());

      $count = 1;
      foreach ($bk_cart_arr as &$value) {   // 一定要传引用
          $index = strpos($value, ':');
          if(substr($value, 0, $index) == $product_id) {
              $count = ((int) substr($value, $index+1)) + 1;
              $value = $product_id . ':' . $count;
              break;
          }
      }

      if($count == 1) {
          array_push($bk_cart_arr, $product_id . ':' . $count);
      }

      return response($m3_result->toJson())->withCookie('bk_cart', implode(',', $bk_cart_arr));
  }

  //参数购物车
    public function deleteCart(Request $request)
    {
        //先定义返回值 反正回经过校验 =不通过结果会变化
        $m3_result = new M3Result();
        $m3_result->status=0;
        $m3_result->message = '删除成功';

        $product_ids = $request->input('product_ids','');
        if($product_ids=='') {
            $m3_result->status = 1;
            $m3_result->message = '书籍ID为空';
            return $m3_result;
        }

        $product_ids_arr = explode(',', $product_ids);

        //获取用户登录信息
        $member = $request->session()->get('member', '');
        if($member != '') {
            // 已登录 删除
            CartItem::whereIn('product_id', $product_ids_arr)->delete();
            return $m3_result->toJson();
        }

        $product_ids = $request->input('product_ids', '');
        if($product_ids == '') {
            $m3_result->status = 1;
            $m3_result->message = '书籍ID为空';
            return $m3_result->toJson();
        }
        //分割字符串
        $product_ids_arr= explode(',',$product_ids);
        // 未登录
        $bk_cart = $request->cookie('bk_cart');
        $bk_cart_arr = ($bk_cart!=null?explode(',',$bk_cart):array());
        foreach($bk_cart_arr as $key => $value)
        {
            $index = strpos($value,':');
            $product_id = substr($value,0,$index);
            //存在 删除
            if(in_array($product_id,$product_ids_arr))
            {
                //删除数组中的元素
                array_splice($bk_cart_arr,$key,1);
                continue;
            }
        }


        //将删除操作后的cookie存入cookie
        return response($m3_result->toJson())->withCookie('bk_cart',implode(',',$bk_cart_arr));

    }

}
