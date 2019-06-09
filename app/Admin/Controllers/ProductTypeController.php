<?php

namespace App\Admin\Controllers;

use App\Models\ProductType;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductTypeController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('类型管理')
            ->description('产品类型管理')
            ->body($this->grid());
    }


    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('类型管理')
            ->description('编辑产品类型')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('类型管理')
            ->description('创建产品类别')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductType);

        $grid->model()->orderBy('is_show', 'desc')->orderBy('order', 'asc');

        $grid->name('类型名称');
        $is_show = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $grid->is_show('显示')->switch($is_show);
        $grid->order('排序');
        $grid->created_at('创建时间');


        // 过滤器
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->column(6, function($filter){
                $filter->like('name', '类别名称');
            });
        });

        // 关闭视图
        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        $grid->disableExport();

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ProductType);

        $form->text('name', '类型名称')->required();
        $is_show = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $form->switch('is_show', '显示')->states($is_show)->default(1);
        $form->number('order', '排序')->default(1);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }
}
