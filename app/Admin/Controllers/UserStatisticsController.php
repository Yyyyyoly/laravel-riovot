<?php

namespace App\Admin\Controllers;

use App\Models\AdminUser;
use App\Http\Controllers\Controller;
use App\Models\UserApplyProduct;
use App\Models\UserStatistic;
use App\Models\User;
use Carbon\Carbon;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;

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
     * 删除
     *
     * @param $id
     *
     * @return int
     */
    public function destroy($id)
    {
        return UserStatistic::destroy($id);
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
        $name = request('name');

        $grid = new Grid(new UserStatistic());

        $grid->header(function () use ($start_time, $end_time, $name) {
            $row = new Row();

            // 用户注册汇总
            $user_register_sql = User::where('registered_at', '>=', $start_time)
                ->where('registered_at', '<=', $end_time);
            // 用户申请汇总
            $user_apply_sql = UserApplyProduct::where('created_at', '>=', $start_time)
                ->where('created_at', '<=', $end_time);
            // 如果有渠道限制
            if ($name) {
                $admin_ids = AdminUser::where('name', 'like', "%{$name}%")->get()->pluck('id')->toArray();
                $user_register_sql->whereIn('admin_id', $admin_ids);
                $user_apply_sql->whereIn('admin_id', $admin_ids);
            }

            $user_register_total = $user_register_sql->count();
            $user_apply_total = $user_apply_sql->count();

            $box_register = new Box("注册汇总", empty($user_register_total) ? '--' : $user_register_total);
            $box_register->style('info');
            $row->column(2, $box_register);

            $box_apply = new Box("申请汇总", empty($user_apply_total) ? '--' : $user_apply_total);
            $box_apply->style('info');
            $row->column(2, $box_apply);

            return $row;
        });


        $grid->name('渠道名称');
        $grid->column('register_count', '注册量')->display(function ($register_count) {
            return intval($register_count);
        })->sortable();
        $grid->column('apply_count', '申请量')->display(function ($apply_count) {
            return intval($apply_count);
        })->sortable();

        // 过滤器
        $grid->filter(function (Grid\Filter $filter) use ($start_time, $end_time) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->column(6, function ($filter) use ($start_time, $end_time) {
                $filter->like('name', '渠道名称');
                $filter->between('time', '时间')->datetime()->default([
                    'start' => $start_time,
                    'end'   => $end_time,
                ]);
            });
        });

        $grid->disableCreateButton();
        $grid->disableRowSelector();
        $grid->disableActions();
        $grid->disableExport();
        $grid->disableColumnSelector();

        return $grid;
    }
}
