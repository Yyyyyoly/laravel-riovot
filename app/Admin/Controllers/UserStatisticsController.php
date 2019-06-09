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
        $name = request('name');
        $time = request('time');
        if (empty($time)) {
            $start_time = Carbon::now()->startOfDay()->toDateTimeString();
            $end_time = Carbon::now()->endOfDay()->toDateTimeString();
            request()->offsetSet('created_at', ['start' => $start_time, 'end' => $end_time]);
        } else {
            $start_time = empty($time['start']) ? null : Carbon::parse($time['start'])->toDateTimeString();
            $end_time = empty($time['end']) ? null : Carbon::parse($time['end'])->toDateTimeString();
        }

        $grid = new Grid(new UserStatistic());

        $grid->header(function () use ($start_time, $end_time, $name) {
            $row = new Row();

            // 用户注册汇总
            $user_register_sql = User::query();
            // 用户申请汇总
            $user_apply_sql = UserApplyProduct::query();

            if ($start_time) {
                $user_register_sql->where('registered_at', '>=', $start_time);
                $user_apply_sql->where('created_at', '>=', $start_time);
            }

            if ($end_time) {
                $user_register_sql->where('registered_at', '<=', $end_time);
                $user_apply_sql->where('created_at', '<=', $end_time);
            }

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
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->column(6, function ($filter) {
                $filter->like('name', '渠道名称');
                $filter->between('time', '时间')->datetime();
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
