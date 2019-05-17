<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

// 这是一个不存在的类。只为了渠道统计存在
class UserStatistic extends Model
{
    /**
     * @param int    $perPage
     * @param array  $columns
     * @param string $pageName
     * @param null   $page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $admin_model = config('admin.database.users_model');

        // 总条数
        $total = $admin_model::count();

        // 分页
        $perPage = request('per_page', $perPage);
        $page = request('page', 1);
        $start = ($page - 1) * $perPage;

        // where条件
        $time = request('time');
        $start_time = empty($time) ? Carbon::now()->startOfDay() : Carbon::parse($time['start']);
        $end_time = empty($time) ? Carbon::now()->endOfDay() : Carbon::parse($time['end']);

        // 排序条件
        $sort = request('_sort', []);

        if (empty($sort)) {
            $data = $admin_model::selectRaw('id as admin_id, name')->limit($perPage)->offset($start)->get();
        } else {
            if ($sort['column'] == 'register_count') {
                $admin_table_name = $admin_model::getModel()->getTable();
                $register_table = User::getModel()->getTable();
                $data = $admin_model::table($admin_table_name . ' as a')
                    ->leftJoin($register_table . ' as b', 'b.admin_id', '=', 'a.id')
                    ->where('registered_at', '>=', $start_time)
                    ->where('registered_at', '<=', $end_time)
                    ->groupBy('admin_id, name')
                    ->selectRaw('admin_id, name, count(*) as count')
                    ->orderBy('count', $sort['type'])
                    ->limit($perPage)
                    ->offset($start)
                    ->get();
            } elseif ($sort['column'] == 'apply_count') {
                $admin_table_name = $admin_model::getModel()->getTable();
                $apply_table = UserApplyProduct::getModel()->getTable();
                $data = $admin_model::table($admin_table_name . ' as a')
                    ->leftJoin($apply_table . ' as b', 'b.admin_id', '=', 'a.id')
                    ->where('created_at', '>=', $start_time)
                    ->where('created_at', '<=', $end_time)
                    ->groupBy('admin_id')
                    ->selectRaw('admin_id, name, count(*) as count')
                    ->orderBy('count', $sort['type'])
                    ->limit($perPage)
                    ->offset($start)
                    ->get();
            } else {
                $data = $admin_model::selectRaw('id as admin_id, name')->limit($perPage)->offset($start)->get();
            }
        }

        $ids = [];
        foreach ($data as $d) {
            $ids[] = $d->admin_id;
        }

        if ($ids) {
            // 渠道注册统计
            $register_datastatistics = User::where('registered_at', '>=', $start_time)
                ->where('registered_at', '<=', $end_time)
                ->whereIn('admin_id', $ids)
                ->groupBy('admin_id')
                ->selectRaw('admin_id, count(*) as count')
                ->get()
                ->pluck('count', 'admin_id')
                ->toArray();

            // 渠道申请统计
            $apply_datastatistics = UserApplyProduct::where('created_at', '>=', $start_time)
                ->where('created_at', '<=', $end_time)
                ->whereIn('admin_id', $ids)
                ->groupBy('admin_id')
                ->selectRaw('admin_id, count(*) as count')
                ->get()
                ->pluck('count', 'admin_id')
                ->toArray();
        }


        $format = [];
        foreach ($data as $item) {
            $format[] = [
                'id'             => $item->admin_id,
                'name'           => $item->name,
                'register_count' => $register_datastatistics[$item->admin_id] ?? '--',
                'apply_count'    => $apply_datastatistics[$item->admin_id] ?? '--',
            ];
        }

        $statics = static::hydrate($format);

        $paginator = new LengthAwarePaginator($statics, $total, $perPage);

        $paginator->setPath(url()->current());

        return $paginator;
    }


    public static function with($relations)
    {
        return new static;
    }

}

