<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin//中间件需要到 Kernel.php注册 然后到路由中设置
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $http_referer = $_SERVER['HTTP_REFERER'];
        //获取用户信息
        $member = $request->session()->get('member', '');
        if($member == '') {//用户信息为空表示未登录  因为登陆完成会写入session
          //获取传递来的页面 也就是上一次的页面 确保用户完成登录后跳转 PHP自带方法
          //$return_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 这里获得提交订单的页面被改成了POST所以失效了
          //用户未登录 重定向刚刚到登录界面 通过路由触发视图的用户控制器
          //return redirect('/login?return_url=' . urlencode($return_url));
            return redirect('/login?return_url=' . urlencode($http_referer));
        }

        //登陆了就什么都处理返回到下一步 目标页面
        return $next($request);
    }

}
