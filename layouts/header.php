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
<body class="hold-transition skin-green sidebar-mini">
<!-- Site wrapper -->

<div class="wrapper">
    <style>
        table td, table th {
            text-align: center;
        }
    </style>
    <header class="main-header">
        <!-- Logo -->
        <a href="default" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><i class="fa fa-bar-chart-o" aria-hidden="true"></i></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><?php echo Config::$_TITLE_APP ?></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="logout">
                            <i class="fa fa-sign-out" aria-hidden="true"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="images/images.jpg" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p> Paul G Ottenwalder </p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">MAIN NAVIGATION</li>
                <li>
                    <a href="default"> <i class="fa fa-home"></i> <span>Home</span>
                    </a>
                </li>
                <!-- reportes dropdown -->
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-signal" aria-hidden="true"></i> <span>Reportes</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="reportProduccion"><i class="fa fa-circle-o"></i>Produccion</a></li>
                        <li><a href="reportProblem"><i class="fa fa-circle-o"></i>Problemas</a></li>
                        <li><a href="reportEarning"><i class="fa fa-circle-o"></i>Ganancia</a></li>
                    </ul>
                </li>
            </ul>

        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- =============================================== -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div id="loader-wrapper">
            <div id="loader"></div>

            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>

        </div>