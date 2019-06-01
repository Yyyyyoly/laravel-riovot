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
        $is_admin = \Admin::user()->inRoles(config('admin.admin_role_name'));

        if ($is_admin) {
            $body = $this->adminGrid();
        } else {
            $body = $this->grid();
        }

        return $content
            ->header('用户管理')
            ->description('用户登录日志')
            ->body($body);
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
        return UserLoginLog::destroy($id);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function adminGrid()
    {
        $created_at = request('created_at');
        if (empty($created_at)) {
            $start_at = Carbon::now()->startOfDay();
            $end_at = Carbon::now()->endOfDay();
            request()->offsetSet('created_at', ['start' => $start_at, 'end' => $end_at]);
        }

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

            $filter->column(6, function ($filter) {
                $filter->like('adminUser.name', '渠道名称');
                $filter->like('user.name', '用户姓名');
                $filter->like('user.phone', '手机号');
                $filter->between('created_at', '登录时间')->datetime();
            });
        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
            $actions->disableEdit();
        });
        $grid->disableCreateButton();

        return $grid;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $admin_user = \Admin::user();

        $grid = new Grid(new UserLoginLog());

        // 非管理员只能查看本人
        $grid->model()->where('admin_id', $admin_user->id);

        if (empty($created_at)) {
            $start_at = Carbon::now()->startOfDay();
            $end_at = Carbon::now()->endOfDay();
            request()->offsetSet('created_at', ['start' => $start_at, 'end' => $end_at]);
        }

        $grid->with(['user', 'adminUser']);

        $grid->adminUser()->name('渠道名称')->sortable();
        $grid->user()->name('用户姓名')->sortable();
        $grid->user()->phone('手机号');
        $grid->created_at('登录时间')->sortable();

        // 过滤器
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(6, function ($filter) {
                $filter->like('user.name', '用户姓名');
                $filter->like('user.phone', '手机号');
                $filter->between('created_at', '登录时间')->datetime();
            });
        });

        $grid->disableActions();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }
}
