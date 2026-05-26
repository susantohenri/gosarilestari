<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/dataTables.bootstrap4.css') ?>">
<div class="col-sm-12">
    <div class="card card-primary card-outline">
      <div class="card-body">

        <?php if (in_array("create_{$current['controller']}", $permission)) : ?>
        <div class="col-sm-12 text-right">
            <?php if ('Asset' === $current['controller']): ?>
                <label>Item Status</label>
                <select id="filter_asset_status">
                    <option value="Yes">Active</option>
                    <option value="No">Inactive</option>
                </select>
                <a href="<?= site_url($current['controller'] . '/scan') ?>" class="btn btn-primary">
                    Add / Edit <?= $page_title ?>
                </a>
                <a href="<?= site_url($current['controller'] . '/html') ?>" class="btn btn-danger" target="_blank">
                    Export HTML
                </a>
                <a href="<?= site_url($current['controller'] . '/pdf') ?>" class="btn btn-info">
                    Export PDF
                </a>
                <a href="<?= site_url($current['controller'] . '/excel') ?>" class="btn btn-warning">
                    Export Excel
                </a>
            <?php else: ?>
            <a href="<?= site_url($current['controller'] . '/create') ?>" class="btn btn-primary">
                <i class="fa fa-plus"></i>&nbsp;Add New <?= $page_title ?>
            </a>
            <?php endif ?>
        </div>
        <?php endif ?>
        <div class="card-body">

            <table class="table table-bordered table-striped datatable table-model">
                <tfoot>
                    <tr>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div><!-- /.card -->
</div>
<script type="text/javascript">
    var thead = <?= json_encode($thead) ?>;
    var allow_read = <?= in_array("read_{$current['controller']}", $permission) ? 1 : 0 ?>
</script>