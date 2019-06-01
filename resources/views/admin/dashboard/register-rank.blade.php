<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">注册排行榜</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <h5>我的注册排名:&nbsp;&nbsp;<font style="color: red">{{$my_rank}}</font></h5>
                </tr>
                <tr>
                    <td>排名</td>
                    <td width="120px">渠道名称</td>
                    <td>注册人数</td>
                </tr>
                @foreach($rank_list as $index => $rank)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td width="120px">{{ $rank['name'] }}</td>
                        <td>{{ $rank['score'] }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>