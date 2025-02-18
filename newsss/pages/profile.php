<?php
require_once 'support_file.php';

$unique = 'user_id';
$table = "users";
$page = "profile.php";
$crud = new crud($table);

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Directory to store uploaded images
    $uploadDir = "../assets/images/staff/staff/";

    // Ensure the uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Get file information
    $fileName = basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $fileName;
    $uploadOk = 1;

    // Check if file is an image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check !== false) {
        echo "File is an image - " . $check['mime'] . ".<br>";
    } else {
        echo "File is not an image.<br>";
        $uploadOk = 0;
    }

    // Check file size (limit: 2MB)
    if ($_FILES['image']['size'] > 2000000) {
        echo "Sorry, your file is too large.<br>";
        $uploadOk = 0;
    }

    // Allow specific file formats
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.<br>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.<br>";
    } else {
        // Try to upload the file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            echo "The file " . htmlspecialchars($fileName) . " has been uploaded.<br>";

            // Save the uploaded file path to the database
            $crud->update($unique, $_SESSION['userid'], ['picture_url' => $targetFile]);
        } else {
            echo "Sorry, there was an error uploading your file.<br>";
        }
    }
    $fileUrl = $targetFile;
    $updateData = ['picture_url' => $fileUrl];
    $_POST['picture_url'] = $fileUrl;
    $crud->update($unique);
}

// Fetch user details
$users = find_all_field('users', '', 'user_id=' . $_SESSION['userid']);
$title = 'Profile';

$usersFname = @$users->fname;
$usersEmail = @$users->email;
$usersMobile = @$users->mobile;
$usersDesignation = @$users->designation;
$usersStatus = @$users->account_status;
$usersPP = @$users->picture_url;
?>
    <!-- Include header and body content -->
<?php require_once 'header_content.php'; ?>
<?php require_once 'body_content.php'; ?>



    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                <h2>User Details</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form method="POST" class="form-horizontal form-label-left" enctype="multipart/form-data">
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name"></label>
                        <div class="col-md-6 col-sm-6 ">
                            <?php if (empty($usersPP)) {?>
                                <input type="file" name="image">
                            <?php } else { ?>
                                <img src="<?=$usersPP;?>" width="100" height="100"><br><br>
                                <input type="file" name="image">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">User ID</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" name="<?=$unique?>" readonly value="<?=$_SESSION['userid']?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">User Name</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" readonly value="<?=$usersFname?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Email</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" readonly value="<?=$usersEmail?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Mobile</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" readonly value="<?=$usersMobile?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Designation</label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="text" id="first-name" readonly value="<?=$usersDesignation?>" required="required" class="form-control ">
                        </div>
                    </div>
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="first-name">Status</label>
                        <div class="col-md-6 col-sm-6 ">
                            <?php if($usersStatus=='active') { ?>
                                <span class="label label-success" style="font-size:10px">Active</span>
                            <?php } elseif($usersStatus=='inactive') { ?>
                                <span class="label label-danger" style="font-size:10px">Inactive</span>
                            <?php } else { ?>
                                <span class="label label-danger" style="font-size:10px">Banned</span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="item form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <a class="btn btn-danger" href="dashboard.php">Cancel</a>
                            <button type="submit" name="record" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?=$html->footer_content();?>