<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExport\UserLoginCsvExporter;
use App\Http\Controllers\Controller;
use App\Models\UserLoginLog;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class UserLoginController extends Controller
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
            ->description('用户登录日志')
            ->body($this->grid());
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserLoginLog());

        $grid->exporter(new UserLoginCsvExporter());

        $grid->with(['user', 'adminUser']);

        $grid->adminUser()->name('渠道名称')->sortable();
        $grid->user()->name('用户姓名')->sortable();
        $grid->user()->phone('手机号');
        $grid->created_at('登录时间')->sortable();

        // 过滤器
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->like('adminUser.name', '渠道名称');
            $filter->like('user.name', '用户姓名');
            $filter->like('user.phone', '手机号');
            $filter->between('created_at', '登录时间')->datetime()->default([
                'start' => Carbon::now()->startOfDay(),
                'end'   => Carbon::now()->endOfDay(),
            ]);;
        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
            $actions->disableEdit();
        });
        $grid->disableCreateButton();

        return $grid;
    }
}
