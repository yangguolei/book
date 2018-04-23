<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\OrderItem;
use Log;//日志命名空间
class OrderController extends Controller//订单信息
{
    public function toOrderCommit(Request $request)
    {
        //获取post参数post
        $product_ids = $request->input('product_ids','');
        //获取参数 分割字符串 通过URL/参数的参数组会自动已逗号分割
        $product_ids_arr= $product_ids != null ? explode(',', $product_ids) : array();
        //获取用户信息
         $member = $request->session()->get('member','');
        $cart_items =  CartItem::where('member_id',$member->id)->whereIn('product_id',$product_ids_arr)->get();

        //生成订单
        $order = new Order();
        $order->member_id = $member->id;
        $order->save();//保存

        $cart_items_arr = array();
        $total_price = 0;
        $name = '';
        foreach ($cart_items as $cart_item)
        {
            //工具存储的商品id获取商品
            $cart_item->product = Product::find($cart_item->product_id);
            //判断商品是否存在
            if($cart_item->product !=null)
            {
                //计算价格
                $total_price +=$cart_item->product->price*$cart_item->count;
                //字符串拼接要用. 数值可以用+= 但是字符串不行得用.=
                $name .='《'.$cart_item->product->name.'》';
                array_push($cart_items_arr,$cart_item);

                //存储订单商品项
                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->product_id = $cart_item->product_id;
                $order_item->count = $cart_item->count;
                $order_item->pdt_snapshot =json_encode($cart_item->product);
                $order_item->save();
            }
        }


        //保存订单其余信息
        $order ->name=$name;
        $order->total_price = $total_price;
        $order->status=1;//未支付
        $order->order_no = 'E'.time().$order->id;//订单好 E前缀拼接时间戳和 订单id 因为id时自动生存需要保存才有
        $order->save();//保存


        //return $cart_items_arr;
        return view('order_commit')->with('cart_items',$cart_items_arr)
                                        ->with('name',$name)
                                        ->with('order_no',$order->order_no)
                                        ->with('total_price',$total_price);

    }

    //前往订单列表
    public function toOrderList(Request $request)
    {
        //获取用户信息
        $member = $request->session()->get('member', '');
        //获取用户订单信息
        $orders = Order::where('member_id', $member->id)->get();
        //遍历订单信息
        foreach ($orders as $order) {//遍历的如果时对象类型那么不用引用 因为对象之间就是引用关系
            //工具订单id获取订单项
            $order_items = OrderItem::where('order_id', $order->id)->get();
            $order->order_items = $order_items;
            foreach ($order_items as $order_item) {
                //订单快照
                //$order_item->product = json_decode($order_item->pdt_snapshot);
                $order_item->product =Product::find($order_item->product_id);
            }
        }

        return view('order_list')->with('orders', $orders);
    }

}
