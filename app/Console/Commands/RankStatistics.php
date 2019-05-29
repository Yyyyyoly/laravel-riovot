<?php

namespace App\Console\Commands;

use App\Constants\AdminCacheKeys;
use App\Models\UserApplyProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RankStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:rank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计当日排行榜，1分钟更新一次';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();

        $this->registerRankStatistic($now);
        $this->applyRankStatistic($now);
    }


    /**
     * 统计当日注册排行
     *
     * @param Carbon $time
     *
     * @throws \Exception
     */
    public function registerRankStatistic(Carbon $time)
    {
        $timestamp_cache_key = AdminCacheKeys::getRegisterLastUpdateTimestamp();
        $start_time_stamp = redis()->get($timestamp_cache_key);
        $start = empty($start_time_stamp) ? $time->copy()->addMinute(-1) : Carbon::createFromTimestamp($start_time_stamp);
        $end = $time;

        $redis_key = AdminCacheKeys::getRegisterRankKey($time);

        // 统计结果
        $results = User::where('registered_at', '>', $start)
            ->where('registered_at', '<=', $end)
            ->groupBy('admin_id')
            ->selectRaw('admin_id, count(*) as count')
            ->get();

        // redis更新
        foreach ($results as $result) {
            redis()->zincrby($redis_key, $result->count, $result->admin_id);
        }

        redis()->set($timestamp_cache_key, $time->getTimestamp());
    }

    /**
     * 统计当日申请排行
     *
     * @param Carbon $time
     *
     * @throws \Exception
     */
    public function applyRankStatistic(Carbon $time)
    {
        $timestamp_cache_key = AdminCacheKeys::getApplyLastUpdateTimestamp();
        $start_time_stamp = redis()->get($timestamp_cache_key);
        $start = empty($start_time_stamp) ? $time->copy()->addMinute(-1) : Carbon::createFromTimestamp($start_time_stamp);
        $end = $time;

        $redis_key = AdminCacheKeys::getApplyRankKey($time);

        // 统计结果
        $results = UserApplyProduct::where('created_at', '>', $start)
            ->where('created_at', '<=', $end)
            ->groupBy('admin_id')
            ->selectRaw('admin_id, count(*) as count')
            ->get();

        // redis更新
        foreach ($results as $result) {
            redis()->zincrby($redis_key, $result->count, $result->admin_id);
        }

        redis()->set($timestamp_cache_key, $time->getTimestamp());
    }
}
