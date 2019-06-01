<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExport\UserCsvExporter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class UserController extends Controller
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
            ->description('用户列表')
            ->body($body);
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
            ->header('用户管理')
            ->description('编辑用户信息')
            ->body($this->form()->edit($id));
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function adminGrid()
    {
        $registered_at = request('registered_at');
        if (empty($registered_at)) {
            $start_at = Carbon::now()->startOfDay();
            $end_at = Carbon::now()->endOfDay();
            request()->offsetSet('registered_at', ['start' => $start_at, 'end' => $end_at]);
        }

        $grid = new Grid(new User());

        $grid->exporter(new UserCsvExporter());

        $grid->with(['adminUser']);

        $grid->adminUser()->name('渠道名称')->sortable();
        $grid->name('姓名');
        $grid->phone('手机号');
        $grid->age('年龄');
        $grid->ant_scores('芝麻分');
        $grid->registered_at('注册时间')->sortable();

        // 过滤器
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(6, function ($filter) {
                $filter->like('adminUser.name', '渠道名称');
                $filter->like('phone', '手机号');
                $filter->like('name', '姓名');
                $filter->between('registered_at', '注册时间')->datetime();
            });
        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
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

        $grid = new Grid(new User());

        // 非管理员只能查看本人
        $grid->model()->where('admin_id', $admin_user->id);

        $registered_at = request('registered_at');
        if (empty($registered_at)) {
            $start_at = Carbon::now()->startOfDay();
            $end_at = Carbon::now()->endOfDay();
            request()->offsetSet('registered_at', ['start' => $start_at, 'end' => $end_at]);
        }

        $grid->with(['adminUser']);

        $grid->adminUser()->name('渠道名称')->sortable();
        $grid->name('姓名');
        $grid->phone('手机号');
        $grid->age('年龄');
        $grid->ant_scores('芝麻分');
        $grid->registered_at('注册时间')->sortable();

        // 过滤器
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(6, function ($filter) {
                $filter->like('phone', '手机号');
                $filter->like('name', '姓名');
                $filter->between('registered_at', '注册时间')->datetime();
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

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());

        $form->display('name', '姓名');
        $form->display('registered_at', '注册时间');
        $form->password('password', '用户密码')->rules('required|confirmed');
        $form->password('password_confirmation', '确认用户密码')->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });


        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
            $footer->disableCreatingCheck();
        });

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = password_hash($form->password, PASSWORD_BCRYPT);
            }
        });

        return $form;
    }

}
