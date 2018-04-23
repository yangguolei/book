<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use App\Entity\Member;

Route::get('/', function (){
    return redirect('category');
});

Route::get('/login', 'View\MemberController@toLogin');
Route::get('/register', 'View\MemberController@toRegister');
Route::get('/category','View\BookController@toCategory');
Route::get('/product/category_id/{category_id}','View\BookController@toProduct');
Route::get('/product/{product_id}','View\BookController@toPdtContent');
//第二个参数为中间件 如果多个可以给一个字符串数组 中间件注册 Kernel.php
//Route::get('/cart',['middleware'=>'check.login'],'View\CartController@toCart');
Route::get('/cart','View\CartController@toCart');

//分组 指定前缀为service  比如validate_code/create这个路由book.yang.com/service/validate_code/create
Route::group(['prefix'=>'service'],function(){
    Route::get('validate_code/create', 'Service\ValidateController@create');
    Route::post('validate_phone/send', 'Service\ValidateController@sendSMS');
    Route::post('register', 'Service\MemberController@register');
    Route::post('login', 'Service\MemberController@login');
    //ajax提交路由时附带了一个参数 所以/{参数名}接收 然后在相应方法中获取即可 也可以直接URL输入
    Route::get('category/parent_id/{parent_id}', 'Service\BookController@getCategoryByParentId');
    Route::get('cart/add/{product_id}', 'Service\CartController@addCart');
    Route::get('cart/delete', 'Service\CartController@deleteCart');
    //图片上传
    Route::post('upload/{type}', 'Service\UploadController@uploadFile');
});
//中间件分组
Route::group(['middleware'=>'check.login'],function(){

    Route::post('/order_commit', 'View\OrderController@toOrderCommit');
    Route::get('/order_list', 'View\OrderController@toOrderList');
});

//后台
Route::group(['prefix'=>'admin'],function() {
    Route::group(['prefix'=>'service'],function() {
        Route::post('/login', 'Admin\IndexController@login');
        Route::post('/category/add', 'Admin\CategoryController@categoryAdd');
        Route::post('/category/del', 'Admin\CategoryController@categoryDel');
        Route::post('category/edit', 'Admin\CategoryController@categoryEdit');



    });

    Route::get('/login', 'Admin\IndexController@toLogin');
    Route::get('/index', 'Admin\IndexController@toIndex');

    Route::get('/category', 'Admin\CategoryController@toCategory');
    Route::get('/category_add', 'Admin\CategoryController@toCategoryAdd');
    Route::get('/category_edit', 'Admin\CategoryController@toCategoryEdit');

    //商品界面
    Route::get('product', 'Admin\ProductController@toProduct');
    Route::get('product_info', 'Admin\ProductController@toProductInfo');
    Route::get('product_add', 'Admin\ProductController@toProductAdd');
    Route::get('product_edit', 'Admin\ProductController@toProductEdit');
    //会员管理
    Route::get('member', 'Admin\MemberController@toMember');
    Route::get('member_edit', 'Admin\MemberController@toMemberEdit');
    //订单
    Route::get('order', 'Admin\OrderController@toOrder');
    Route::get('order_edit', 'Admin\OrderController@toOrderEdit');
});

//这个方式没有使用 验证邮箱在 MemberController控制器中完成了
//Route::post('service/validate_email', 'Service\ValidateController@validateEmail');
//测试号码
//Route::any('test_phone', 'Service\ValidateController@testPhone');
