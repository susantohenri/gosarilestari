<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="p-4 md:p-6">
    <div class="overflow-x-auto">
      <table class="datatable table-model w-full text-sm">
        <tfoot>
          <tr></tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
<script type="text/javascript">
  var thead = <?= json_encode($thead) ?>;
  var allow_read = <?= in_array("read_{$current['controller']}", $permission) ? 1 : 0 ?>
</script>
