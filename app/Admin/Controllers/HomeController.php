<?php

namespace App\Admin\Controllers;

use App\Models\AdminUser;
use App\Constants\AdminCacheKeys;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    /**
     * index
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        $link = route('productView', ['admin_hash_id' => User::encodeAdminId(\Admin::user()->id)]);

        return $content
            ->header('今日实时Top10排行榜')
            ->description('同分数排名不分先后')
            ->row("我的专属链接：<font style='color: indianred'>{$link}</font>")
            ->row($this->title())
            ->row(function (Row $row) {
                $row->column(1, function (Column $column) {
                });

                $row->column(4, function (Column $column) {
                    $column->append($this->registerRank());
                });

                $row->column(2, function (Column $column) {
                });

                $row->column(4, function (Column $column) {
                    $column->append($this->applyRank());
                });

                $row->column(1, function (Column $column) {
                });
            });
    }

    /**
     * title view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function title()
    {
        return view('admin.dashboard.title');
    }


    /**
     * 注册排行榜
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function registerRank()
    {
        $redis_key = AdminCacheKeys::getRegisterRankKey(Carbon::now());
        $results = redis()->zrevrange($redis_key, 0, 9, 'withscores');
        $ids = array_keys($results);
        $name_list = AdminUser::whereIn('id', $ids)->get()->pluck('name', 'id')->toArray();

        $rank_list = [];
        foreach ($results as $key => $value) {
            $rank_list[] = [
                'name'  => $name_list[$key] ?? '--',
                'score' => $value ?? 0,
            ];
        }

        $my_rank = redis()->zrevrank($redis_key, \Admin::user()->id);
        $my_rank = is_null($my_rank) ? '暂未上榜' : $my_rank + 1;

        return view('admin.dashboard.register-rank', ['rank_list' => $rank_list, 'my_rank' => $my_rank]);
    }


    /**
     * 申请排行榜
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function applyRank()
    {
        $redis_key = AdminCacheKeys::getApplyRankKey(Carbon::now());
        $results = redis()->zrevrange($redis_key, 0, 9, 'withscores');

        $ids = array_keys($results);
        $name_list = AdminUser::whereIn('id', $ids)->get()->pluck('name', 'id')->toArray();

        $rank_list = [];
        foreach ($results as $key => $value) {
            $rank_list[] = [
                'name'  => $name_list[$key] ?? '--',
                'score' => $value ?? 0,
            ];
        }

        $my_rank = redis()->zrevrank($redis_key, \Admin::user()->id);
        $my_rank = is_null($my_rank) ? '暂未上榜' : $my_rank + 1;

        return view('admin.dashboard.apply-rank', ['rank_list' => $rank_list, 'my_rank' => $my_rank]);
    }
}
