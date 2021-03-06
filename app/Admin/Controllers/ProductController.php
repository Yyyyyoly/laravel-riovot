<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Tab;


class ProductController extends Controller
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
        return $content
            ->header('产品管理')
            ->description('产品列表')
            ->body($this->grid());
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
            ->header('产品管理')
            ->description('编辑产品信息')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('产品管理')
            ->description('创建产品')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->model()->orderBy('is_show', 'desc')->orderBy('top', 'desc')->orderBy('order', 'asc');
        $grid->model()->with(['productType']);

        $grid->column('productType.name', '产品类型');
        $grid->name('产品名称');
        $grid->icon_url('logo')->image(Product::getImageHostPrefix(), 45, 45);
        $grid->desc('要点');
        $grid->fake_download_nums('已下款初始值');
        $grid->real_download_nums('真实申请量');
        $grid->url('链接');
        $top = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $grid->top('置顶')->switch($top);
        $is_show = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $grid->is_show('显示')->switch($is_show);
        $grid->order('排序')->editable();
        $grid->created_at('创建时间');


        // 过滤器
        $grid->filter(function (Grid\Filter $filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->column(6, function ($filter) {
                $product_types = ProductType::where('is_show', 1)
                    ->orderByDesc('updated_at')
                    ->get()
                    ->pluck('name', 'id')
                    ->toArray();
                $filter->equal('productType.id', '类别名称')->select($product_types);
                $filter->like('name', '产品名称');
                $filter->equal('is_show', '是否显示')->radio([
                    1 => '是',
                    0 => '否',
                ]);
            });
        });

        // 关闭视图
        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        $grid->disableExport();


        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        $product_types = ProductType::where('is_show', 1)
            ->orderByDesc('updated_at')
            ->get()
            ->pluck('name', 'id')
            ->toArray();
        $form->select('type_id', '产品类型')->options($product_types)->required();
        $form->text('name', '产品名称')->required();
        $form->url('url', '链接')->required();
        $form->text('desc', '要点')->required();
        $form->image('icon_url', 'logo')->move('logos')->uniqueName()->removable()->required();
        $form->number('fake_download_nums', '已下款初始值')->default(rand(10000, 100000));
        $top = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $form->switch('top', '置顶')->states($top)->default(0);
        $is_show = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
        ];
        $form->switch('is_show', '显示')->states($is_show)->default(1);
        $form->number('order', '排序')->default(1);

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
        });

        return $form;
    }

    /**
     * 渠道显示的产品列表
     *
     * @return Content
     */
    public function qudaoList()
    {
        // 引入图标库
        \Admin::css(asset('/css/font-awesome.min.css'));

        $tab = new Tab();
        $product_list = Product::getProductListIgnoreType();
        $html = '';
        foreach ($product_list as $product) {
            $html .= $this->renderProductIcon(
                $product['icon_url'],
                $product['name'],
                $product['download_nums'],
                $product['desc']
            );
        }
        $row = new Row($html);

        $tab->add('无视黑白', $row->render());

        return (new Content())
            ->header('产品管理')
            ->description('产品列表')
            ->body($tab);
    }

    /**
     * 产品模块显示
     *
     * @param $icon_url
     * @param $product_name
     * @param $downloads_num
     * @param $desc
     *
     * @return string
     */
    private function renderProductIcon($icon_url, $product_name, $downloads_num, $desc)
    {
        return
            <<<EOF
<div class="col-md-2 col-xs-6">
<div style="position:relative;padding: 5px;box-shadow: 1px 1px 4px #c9daf6;background: #fff;border: 1px solid #c9daf6;height:100%;
border-radius: 10px 0px 10px 0px;margin-bottom: 10px;text-align: center;margin-left: 2%;">
<i style="color: #c9daf6;position: absolute;font-size:10px;right: 2px;top: 1px;"></i>
<img style="width: 50px;height: 50px;" src="{$icon_url}" />
<div style="text-align: center;background: #FAFAFA;border-bottom: 1px solid #E5E5E7;line-height: 35px;color: #EC983E;
overflow: hidden;text-overflow:ellipsis;word-break: keep-all;white-space: nowrap">{$product_name}</div>
<div style="color: #e95656"><i class="icon-download-alt"></i>{$downloads_num}</span></div>
<div style="color: #666;font-size: 12px;">{$desc}</div>
</div>
</div>
EOF;

    }
}
