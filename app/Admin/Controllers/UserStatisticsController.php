<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserApplyProduct;
use App\Models\UserStatistic;
use App\User;
use Carbon\Carbon;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Collection;

class UserStatisticsController extends Controller
{

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
            ->header('渠道管理')
            ->description('渠道统计')
            ->body($this->grid());
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $time = request('time');
        $start_time = empty($time) ? Carbon::now()->startOfDay() : Carbon::parse($time['start']);
        $end_time = empty($time) ? Carbon::now()->endOfDay() : Carbon::parse($time['end']);

        $grid = new Grid(new UserStatistic());

        $grid->header(function () use ($start_time, $end_time) {
            $row = new Row();

            // 用户注册汇总
            $user_register_total = User::where('registered_at', '>=', $start_time)
                ->where('registered_at', '<=', $end_time)
                ->count();
            $box_register = new Box("注册汇总", empty($user_register_total) ? '--' : $user_register_total);
            $box_register->style('info');
            $row->column(2, $box_register);

            // 用户申请汇总
            $user_apply_total = UserApplyProduct::where('created_at', '>=', $start_time)
                ->where('created_at', '<=', $end_time)
                ->count();
            $box_apply = new Box("申请汇总", empty($user_apply_total) ? '--' : $user_apply_total);
            $box_apply->style('info');
            $row->column(2, $box_apply);

            return $row;
        });


        $grid->name('渠道名称');
        $grid->column('register_count', '注册量')->sortable();
        $grid->column('apply_count', '申请量')->sortable();

        // 过滤器
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('name', '渠道名称');
            $filter->between('time', '时间')->datetime();
        });

        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->disableActions();
        $grid->disableExport();
        $grid->disableColumnSelector();

        return $grid;
    }
}
