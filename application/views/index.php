<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>gosarilestari.com</title>
  <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">

  <link rel="stylesheet" href="<?= base_url('assets/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/adminlte.min.css') ?>">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <style type="text/css">
    a.btn:not([href]):not([tabindex]){color: white}
    .form-child .form-group.row > div {margin: 5px 0}
  </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-light navbar-white">
    <div class="container">
      <a href="<?= base_url() ?>" class="navbar-brand">
        <H2><span class="brand-text font-weight-light"><b>gosarilestari</b>.com</span></H2>
      </a>
      <a href="<?= site_url('Login/Logout') ?>">Logout</a>
    </div>
  </nav>
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
        <?php if (!in_array(current_url(), [site_url(), base_url()])) : ?>
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"><?= $page_title ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
              <?php if (in_array($page_name, ['table', 'dashboard'])): ?>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
              <?php else: ?>
                <li class="breadcrumb-item"><a href="<?= site_url($current['controller']) ?>"><?= $page_title ?></a></li>
                <li class="breadcrumb-item active"><?= ucfirst($page_name) ?></li>
              <?php endif ?>
            </ol>
          </div><!-- /.col -->
          <?php endif ?>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
        <div class="row">
            <?php include "{$page_name}.php" ?>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="container">
      <!-- To the right -->
      <div class="float-right d-none d-sm-inline">
        henry.dinus@gmail.com 081901088918
      </div>
      <!-- Default to the left -->
      <small><strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.</small>
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<!-- <script src="../../dist/js/adminlte.min.js"></script> -->
<script type="text/javascript">
  var site_url = '<?= site_url('/') ?>'
  var current_controller = '<?= $current['controller'] ?>'
  var current_controller_url = '<?= site_url($current['controller']) ?>'
</script>
<?php if (isset($js)): foreach ($js as $script): ?>
  <script type="text/javascript" src="<?= base_url("assets/js/{$script}") ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>