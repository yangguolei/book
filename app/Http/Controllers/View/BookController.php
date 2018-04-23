<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\PdtContent;
use App\Entity\PdtImages;
use App\Entity\CartItem;
use Log;//日志命名空间
class BookController extends Controller
{
    //获取类别
  public function toCategory($value='')
  {
      //获取parent_id为空的记录 也就是一级菜单
      $categorys = Category::whereNull('parent_id')->get();
      //显示并且传递 参数
      return view('category')->with('categorys',$categorys);
  }

  //获取商品
    public function toProduct($category_id)
    {
        //根据类别id读取数据
        $products = Product::where('category_id',$category_id)->get();
        //显示并且传递 参数
        return view('product')->with('products',$products);
    }

    //商品详情
    public function toPdtContent(Request $request,$product_id)
    {
        //获取parent_id是product表的id字段所以可以使用find查找
        $product = Product::find($product_id);
        //但是通过where条件查询就需要配合get或first之类获取记录
        $pdt_content = PdtContent::where('product_id',$product_id)->first();
        //但是通过where条件查询就需要配合get或first之类获取记录
        $pdt_images = PdtImages::where('product_id',$product_id)->get();
        //显示并且传递 参数 向视图传递多个参数

        $count = 0;
        //或群用户数量判断是否登陆
        $member = $request->session()->get('member', '');
        if($member != '') {
            //已登录
            $cart_items = CartItem::where('member_id', $member->id)->get();

            foreach ($cart_items as $cart_item) {
                if($cart_item->product_id == $product_id) {
                    $count = $cart_item->count;
                    break;
                }
            }
        } else {
            //未登录
            //要获得购物车商品个数
            //获取cookie   我们的保存格式 商品id:购物车个数
            $bk_cart = $request->cookie('bk_cart');
            //explode按第一个参数的字符截取字符串 返回一个数组
            //三目运算判断cookie为空返回空数组 不为空按，分割返回字符串数组
            $bk_cart_arr = ($bk_cart!=null ? explode(',', $bk_cart) : array());

            foreach ($bk_cart_arr as $value) {   // 一定要传引用
                $index = strpos($value, ':');
                if(substr($value, 0, $index) == $product_id) {
                    $count = (int) substr($value, $index+1);
                    break;
                }
            }
        }
        //返回参数
        return view('pdt_content')->with('product',$product)
                                       ->with('pdt_content',$pdt_content)
                                       ->with('pdt_images',$pdt_images)
                                       ->with('count',$count);
    }
}
