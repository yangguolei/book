<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Models\M3Result;

class CartController extends Controller
{
    //购物车页面
    public function toCart(Request $request)
    {
        $cart_items = array();
        //获取cookie   我们的保存格式 商品id:购物车个数
        $bk_cart = $request->cookie('bk_cart');
        //explode按第一个参数的字符截取字符串 返回一个数组
        //三目运算判断cookie为空返回空数组 不为空按，分割返回字符串数组
        $bk_cart_arr = $bk_cart != null ? explode(',', $bk_cart) : array();

        //获取用户是否登陆决定购物车是否链接数据库操作
        $member = $request->session()->get('member', '');
        if ($member != '') {
            //同步购物车 参数一 用户id 参数二本地购物车信息  方法在本控制器下面
            $cart_items = $this->syncCart($member->id, $bk_cart_arr);//同步购物车
            //返回视图  传递同步到的数据库文件并且 登陆了那么清空cookie
            return response()->view('cart', ['cart_items' => $cart_items])->withCookie('bk_cart', null);
        }

        //遍历分割号的cookie数组 完成未登录购物车的详细
        foreach ($bk_cart_arr as $key => $value) {
            $index = strpos($value, ':');
            $cart_item = new CartItem();
            $cart_item->id = $key;
            $cart_item->product_id = substr($value, 0, $index);
            $cart_item->count = (int)substr($value, $index + 1);
            //获取到详细的商品记录 方便调用价格预览图等等
            $cart_item->product = Product::find($cart_item->product_id);
            if ($cart_item->product != null) {
                //追加
                array_push($cart_items, $cart_item);
            }
        }
        //返回视图 并向视图返回商品项
        return view('cart')->with('cart_items', $cart_items);
    }

    //购物车同步
    private function syncCart($member_id, $bk_cart_arr)
    {
        $cart_items = CartItem::where('member_id', $member_id)->get();

        $cart_items_arr = array();
        //获取本地数据
        foreach ($bk_cart_arr as $value) {
            $index = strpos($value, ':');
            $product_id = substr($value, 0, $index);
            $count = (int) substr($value, $index+1);

            // 判断离线购物车中product_id 是否存在 数据库中
            $exist = false;
            //判断每条cookie离线商品个数和数据同类商品个数  以多为主 如果本地的多写入数据库
            foreach ($cart_items as $temp) {
                if($temp->product_id == $product_id) {
                    if($temp->count < $count) {
                        $temp->count = $count;
                        $temp->save();
                    }
                    $exist = true;
                    break;
                }
            }

            // 数据库不存在的本地购物车商品则存储进来
            if($exist == false) {
                $cart_item = new CartItem;
                $cart_item->member_id = $member_id;
                $cart_item->product_id = $product_id;
                $cart_item->count = $count;
                $cart_item->save();
                //获取每个对象附加产品对象便于显示
                $cart_item->product = Product::find($cart_item->product_id);
                //存入数组
                array_push($cart_items_arr, $cart_item);
            }
        }

        // 为每个对象附加产品对象便于显示
        foreach ($cart_items as $cart_item) {
            $cart_item->product = Product::find($cart_item->product_id);
            //存入数组
            array_push($cart_items_arr, $cart_item);
        }

        return $cart_items_arr;
    }
}
