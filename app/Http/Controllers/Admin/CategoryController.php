<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Entity\Category;
use App\Models\M3Result;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function toCategory()
    {
        //取出所有记录
        $categories = Category::all();
        foreach ($categories as $category) {
            //当父类id不存在 并且本身不为空
            if($category->parent_id != null && $category->parent_id != '') {
                //根据父类id获取父类的记录 因为我们的一级类别和二级类别在同一张表
                $category->parent = Category::find($category->parent_id);
            }
        }
        //返回窗口 传递参数
        return view('admin.category')->with('categories', $categories);
    }

    //增加类别
    public function  toCategoryAdd() {
        //获取父类为空的记录  提供给添加类别 显示一级类别 parent_id为空的是一级类别
        $categories = Category::whereNull('parent_id')->get();
        //返回窗口 传递参数
        return view('admin.category_add')->with('categories', $categories);
    }

    //编辑界面
    public function toCategoryEdit(Request $request) {
        $id = $request->input('id', '');
        //根据id获取对应记录
        $category = Category::find($id);
        //获得一级类别记录 编辑页面要展示相应列表
        $categories = Category::whereNull('parent_id')->get();

        return view('admin/category_edit')->with('category', $category)
                                               ->with('categories', $categories);
    }

    /********************Service*********************/
    public function categoryAdd(Request $request) {
        //input('name', '') 获取post请求的键 取不到第二个参数为默认值
        $name = $request->input('name', '');
        $category_no = $request->input('category_no', '');
        $parent_id = $request->input('parent_id', '');
        $preview = $request->input('preview', '');

        //新建实体对象 准备插入
        $category = new Category;
        $category->name = $name;
        $category->category_no = $category_no;
        $category->preview = $preview;
        //$parent_id不为空那么就读取保存
        if($parent_id != '') {
            $category->parent_id = $parent_id;
        }
        //保存记录
        $category->save();

        //自定义的结果接口
        $m3_result = new M3Result;
        $m3_result->status = 0;
        $m3_result->message = '添加成功';

        return $m3_result->toJson();
    }
    //删除
    public function categoryDel(Request $request) {
        //获取post提交上来要删除的id
        $id = $request->input('id', '');
        //获取通过因为设置了id为主键所以 通过find查找主键为id的记录 然后删除
        Category::find($id)->delete();

        $m3_result = new M3Result;
        $m3_result->status = 0;
        $m3_result->message = '删除成功';

        return $m3_result->toJson();
    }
    //编辑
    public function categoryEdit(Request $request) {
        $id = $request->input('id', '');
        //根据id获取类别记录
        $category = Category::find($id);

        //获取提交的参数
        $name = $request->input('name', '');
        $category_no = $request->input('category_no', '');
        $parent_id = $request->input('parent_id', '');
        $preview = $request->input('preview', '');

        //进行字段更新替换
        $category->name = $name;
        $category->category_no = $category_no;
        if($parent_id != '') {
            $category->parent_id = $parent_id;
        }
        $category->preview = $preview;
        //保存
        $category->save();

        $m3_result = new M3Result;
        $m3_result->status = 0;
        $m3_result->message = '添加成功';

        return $m3_result->toJson();
    }
}
