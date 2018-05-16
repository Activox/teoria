<?php

use lib\Config;

$session = \Factory::getSession();
$path = _HOST_ . _DIRECTORY_ . _DS_;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo Config::$_TITLE_APP ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?= $path ?>lib/vendor/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $path ?>lib/vendor/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?= $path ?>lib/vendor/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= $path ?>lib/vendor/bower_components/select2/dist/css/select2.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="<?= $path ?>lib/vendor/bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet"
          href="<?= $path ?>lib/vendor/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <!-- DataTables -->
    <link rel="stylesheet"
          href="<?= $path ?>lib/vendor/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet"
          href="<?= $path ?>lib/vendor/plugins/pace/pace.min.css">
    <!--    Alertify-->
    <link rel="stylesheet" href="<?= $path ?>css/alertify.css">
    <link rel="stylesheet" href="<?= $path ?>css/yaroa.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $path ?>lib/vendor/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?= $path ?>lib/vendor/dist/css/skins/_all-skins.min.css">
    <!-- jQuery 3 -->
    <script src="<?= $path ?>lib/vendor/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?= $path ?>lib/vendor/bower_components/PACE/pace.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?= $path ?>lib/vendor/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- DataTables -->
    <script src="<?= $path ?>lib/vendor/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?= $path ?>lib/vendor/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="<?= $path ?>lib/vendor/bower_components/select2/dist/js/select2.full.min.js"></script>
    <!-- InputMask -->
    <script src="<?= $path ?>lib/vendor/plugins/input-mask/jquery.inputmask.js"></script>
    <script src="<?= $path ?>lib/vendor/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
    <script src="<?= $path ?>lib/vendor/plugins/input-mask/jquery.inputmask.extensions.js"></script>
    <!-- date-range-picker -->
    <script src="<?= $path ?>lib/vendor/bower_components/moment/min/moment.min.js"></script>
    <script src="<?= $path ?>lib/vendor/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap datepicker -->
    <script src="<?= $path ?>lib/vendor/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!--    Script Common-->
    <script src="<?= $path ?>js/script.js"></script>
    <!--    jquery.number-->
    <script src="<?= $path ?>js/jquery.number.js"></script>
    <!--    Alertify-->
    <script src="<?= $path ?>js/alertify.js"></script>
    <!-- SlimScroll -->
    <script src="<?= $path ?>lib/vendor/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="<?= $path ?>lib/vendor/bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= $path ?>lib/vendor/dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?= $path ?>lib/vendor/dist/js/demo.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script>
        $(document).ready(function () {
            $('.sidebar-menu').tree()
        })
    </script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body >