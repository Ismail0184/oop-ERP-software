<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plain Page</title>
    <link href="../assets/vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="../assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="../assets/vendors/nprogress/nprogress.css" rel="stylesheet">
    <link href="../assets/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="../assets/build/css/custom.min.css" rel="stylesheet">
    <link href="../assets/vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css" rel="stylesheet"/>
    <style>
        input[type=text] {
            font-size: 11px;
        }
        .top_nav {
            background-color: #337ab7;
            padding: 10px;
            color: white;
        }
        .x_panel {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Top Navigation -->
    <div class="top_nav">
        <h2 style="margin: 0; font-size: 18px; font-weight: bold;">
            <i class="fa fa-bars" style="color: #f05623;"></i> ICP DISTRIBUTION
        </h2>
    </div>

    <!-- Page Content -->
    <div class="right_col" role="main">
        <div class="x_panel">
            <div class="x_title">
                <h2>Plain Page</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                Add content to the page ...
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="../assets/vendors/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../assets/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Select2 -->
<script src="../assets/vendors/select2/dist/js/select2.full.min.js"></script>
<script>
    $(document).ready(function () {
        $(".select2_single").select2({
            placeholder: "Select a Choose",
            allowClear: true
        });
    });
</script>
</body>
</html>
