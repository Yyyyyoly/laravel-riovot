<?php
/**
 * Created by PhpStorm.
 * User: wangyg
 * Date: 2018/11/28
 * Time: 21:42
 */


namespace App\Admin\Extensions\ExcelExport;

use App\Models\AdminUser;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsDailyReadStatistics;
use App\Models\NewsHotList;
use App\Models\NewsNote;
use App\Models\NewsReport;
use App\Models\Product;
use App\Models\UserApplyProduct;
use App\Models\UserLoginLog;
use App\Models\Video;
use App\Models\VideoCategory;
use App\User;
use Carbon\Carbon;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporter;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Psr\Log\NullLogger;

class UserApplyCsvExporter extends AbstractExporter
{
    /**
     * {@inheritdoc}
     */
    public function export()
    {

        $filename = '用户申请录日志.csv';

        $headers = [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // 标题列
            $titles = [
                '渠道名称',
                '姓名',
                '手机号',
                '产品名称',
                '申请时间',
            ];
            // Add CSV headers
            $this->fputcsv_gb2312($handle, $titles);

            $this->chunk(function ($records) use (&$handle, &$titles) {
                foreach ($records as $record) {
                    $this->fputcsv_gb2312($handle, get_object_vars($record));
                }
            });

            // Close the output stream
            fclose($handle);
        }, 200, $headers)->send();

        exit;
    }


    /**
     * 获取数据集
     *
     * @param callable $callback
     * @param int      $count
     *
     * @return bool|Collection
     */
    public function chunk(callable $callback, $count = 5000)
    {
        $params = Input::all();

        $admin_table = (new AdminUser)->getTable();
        $user_table = (new User)->getTable();
        $user_apply_table = (new UserApplyProduct)->getTable();
        $product_table = (new Product)->getTable();

        $q = \DB::table($user_apply_table)
            ->leftJoin($admin_table, "{$user_apply_table}.admin_id", '=', "{$admin_table}.id")
            ->leftJoin($product_table, "{$user_apply_table}.product_id", '=', "{$product_table}.id")
            ->leftJoin($user_table, "{$user_apply_table}.user_id", '=', "{$user_table}.id")
            ->selectRaw("
                {$admin_table}.name as admin_name, 
                {$user_table}.name as user_name,
                phone,
                {$product_table}.name as product_name,
               {$user_apply_table}.created_at as created_at
            ");


        // conditions
        // admin_name
        if (isset($params['adminUser']) && $params['adminUser']['name']) {
            $q->where("{$admin_table}.name", 'like', $params['adminUser']['name']);
        }

        // phone
        if (isset($params['user.phone']) && $params['user.phone']) {
            $q->where("phone", 'like', $params['phone']);
        }

        // user_name
        if (isset($params['user.name']) && $params['user.name']) {
            $q->where("{$user_table}.name", 'like', $params['user']['name']);
        }

        // product_name
        if (isset($params['product.name']) && $params['product.name']) {
            $q->where("{$product_table}.name", 'like', $params['product']['name']);
        }

        // 登录时间
        if (isset($params['created_at']['start']) && $params['created_at']['start']) {
            $q->where("{$user_apply_table}.created_at", '>=', $params['created_at']['start']);
        }
        if (isset($params['created_at']['end']) && $params['created_at']['end']) {
            $q->where("{$user_apply_table}.created_at", '<=', $params['created_at']['end']);
        }

        // sorts
        $sortArray = $params['_sort'] ?? [];
        if ($sortArray) {
            if ($sortArray['column'] == 'created_at') {
                $sortArray['column'] = "{$user_apply_table}.created_at";
            }
            $q->orderBy($sortArray['column'], $sortArray['type']);
        } else {
            $q->orderBy("{$user_apply_table}.created_at", 'desc');
        }

        // paginate withScope
        $scope = $params['_export_'] ?? null;
        if ($scope && $scope != Exporter::SCOPE_ALL) {
            list($scope, $args) = explode(':', $scope);

            if ($scope == Exporter::SCOPE_CURRENT_PAGE) {
                $pageNum = intval($args);
                $perPage = $params['per_page'] ?? $this->grid->perPage;
                $q->paginate($perPage, ['*'], 'page', $pageNum);

                return $q->get()->chunk($count)->each($callback);
            }

            if ($scope == Exporter::SCOPE_SELECTED_ROWS) {
                $selected = explode(',', $args);
                if ($selected) {
                    $q->whereIn("{$user_apply_table}.id", $selected);
                }
            }
        }

        return $q->chunk($count, $callback);
    }


    /**
     * 中文乱码 将UTF-8转为GB2312编码
     *
     * @param        $handle
     * @param array  $fields
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape_char
     */
    function fputcsv_gb2312($handle, array $fields, $delimiter = ",", $enclosure = '"', $escape_char = "\\")
    {
        foreach ($fields as $k => $v) {
            $fields[$k] = iconv("UTF-8", "GB2312//IGNORE", $v);
        }
        fputcsv($handle, $fields, $delimiter, $enclosure, $escape_char);
    }
}
