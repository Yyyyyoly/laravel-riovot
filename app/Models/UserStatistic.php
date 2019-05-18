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
     * 覆盖分页方法
     *
     * @param int $perPage
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    public function paginate($perPage = 15)
    {
        // table_name
        $admin_model = config('admin.database.users_model');
        $admin_table = $admin_model::getModel()->getTable();
        $user_table = User::getModel()->getTable();
        $user_apply_table = UserApplyProduct::getModel()->getTable();

        // where条件
        // 时间
        $time = request('time');
        $start_time = empty($time) ? Carbon::now()->startOfDay() : Carbon::parse($time['start']);
        $end_time = empty($time) ? Carbon::now()->endOfDay() : Carbon::parse($time['end']);
        // 渠道名称
        $name = request('name');

        // 分页
        $perPage = request('per_page', $perPage);
        $page = request('page', 1);
        $start = ($page - 1) * $perPage;

        // 总条数
        $total = empty($name) ? $admin_model::count() : $admin_model::where('name', 'like', "%{$name}%")->count();

        // 排序条件
        $sort = request('_sort', ['column' => 'id', 'type' => 'asc']);

        // 统计注册量
        $sub_query_1 = \DB::table($admin_table . ' as a')
            ->leftJoin($user_table . ' as b', 'b.admin_id', '=', 'a.id')
            ->where('registered_at', '>=', $start_time)
            ->where('registered_at', '<=', $end_time)
            ->groupBy('admin_id')
            ->selectRaw('admin_id, count(*) as register_count');
        if ($name) {
            $sub_query_1->where('a.name', 'like', "%{$name}%");
        }

        // 统计申请量
        $sub_query_2 = \DB::table($admin_table . ' as a')
            ->leftJoin($user_apply_table . ' as b', 'b.admin_id', '=', 'a.id')
            ->where('b.created_at', '>=', $start_time)
            ->where('b.created_at', '<=', $end_time)
            ->groupBy('admin_id')
            ->selectRaw('admin_id, count(*) as apply_count');
        if ($name) {
            $sub_query_2->where('a.name', 'like', "%{$name}%");
        }

        $query = \DB::table("{$admin_table} as admin")
            ->leftJoin(\DB::raw('(' . $sub_query_1->toSql() . ') AS user'),
                function ($join) use ($sub_query_1) {
                    $join->on('admin.id', '=', 'user.admin_id')
                        ->addBinding($sub_query_1->getBindings());
                })
            ->leftJoin(\DB::raw('(' . $sub_query_2->toSql() . ') AS apply'),
                function ($join) use ($sub_query_2) {
                    $join->on('admin.id', '=', 'apply.admin_id')
                        ->addBinding($sub_query_2->getBindings());
                })
            ->selectRaw('id, name,register_count,apply_count');
        if ($name) {
            $query->where('admin.name', 'like', "%{$name}%");
        }
        $results = $query->orderBy($sort['column'], $sort['type'])
            ->limit($perPage)
            ->offset($start)
            ->get()
            ->toArray();

        $statics = static::hydrate($results);

        $paginator = new LengthAwarePaginator($statics, $total, $perPage);

        $paginator->setPath(url()->current());

        return $paginator;
    }


    public static function with($relations)
    {
        return new static;
    }

}

