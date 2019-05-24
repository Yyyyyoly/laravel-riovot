<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExport\UserApplyCsvExporter;
use App\Http\Controllers\Controller;
use App\Models\UserApplyProduct;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class UserApplyProductController extends Controller
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
            ->header('用户管理')
            ->description('用户申请日志')
            ->body($this->grid());
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserApplyProduct());

        $grid->exporter(new UserApplyCsvExporter());

        $grid->with(['user', 'product', 'adminUser']);

        $grid->adminUser()->name('渠道名称')->sortable();
        $grid->user()->name('用户姓名')->sortable();
        $grid->user()->phone('手机号');
        $grid->product()->name('产品名称')->sortable();
        $grid->created_at('申请时间')->sortable();

        // 过滤器
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(6, function ($filter) {
                $filter->like('adminUser.name', '渠道名称');
                $filter->like('user.name', '用户姓名');
                $filter->like('user.phone', '手机号');
                $filter->like('product.name', '产品名称');
                $filter->between('created_at', '申请时间')->datetime()->default([
                    'start' => Carbon::now()->startOfDay(),
                    'end'   => Carbon::now()->endOfDay(),
                ]);
            });
        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
            $actions->disableEdit();
        });
        $grid->disableCreateButton();

        return $grid;
    }
}