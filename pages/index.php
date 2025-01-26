<?php
$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
$access_time=$dateTime->format("Y-m-d, h:i:s A");
$key = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));

$userAgent = $_SERVER['HTTP_USER_AGENT'];
$browser="";
if(strrpos(strtolower($userAgent),strtolower("MSIE")))
{$browser="Internet Explorer";
} else if(strrpos(strtolower($userAgent),strtolower("Presto")))
{$browser="Opera";
} else if(strrpos(strtolower($userAgent),strtolower("CHROME")))
{$browser="Google Chrome";
} else if(strrpos(strtolower($userAgent),strtolower("SAFARI")))
{$browser="Safari";
} else if(strrpos(strtolower($userAgent),strtolower("FIREFOX")))
{ $browser="FIREFOX";} else
{ $browser="OTHER";}

function get_operating_system() {
    $u_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $operating_system = 'Unknown Operating System';

    //Get the operating_system name
    if($u_agent) {
        if (preg_match('/linux/i', $u_agent)) {
            $operating_system = 'Linux';
        } elseif (preg_match('/macintosh|mac os x|mac_powerpc/i', $u_agent)) {
            $operating_system = 'Mac';
        } elseif (preg_match('/windows|win32|win98|win95|win16/i', $u_agent)) {
            $operating_system = 'Windows';
        } elseif (preg_match('/ubuntu/i', $u_agent)) {
            $operating_system = 'Ubuntu';
        } elseif (preg_match('/iphone/i', $u_agent)) {
            $operating_system = 'IPhone';
        } elseif (preg_match('/ipod/i', $u_agent)) {
            $operating_system = 'IPod';
        } elseif (preg_match('/ipad/i', $u_agent)) {
            $operating_system = 'IPad';
        } elseif (preg_match('/android/i', $u_agent)) {
            $operating_system = 'Android';
        } elseif (preg_match('/blackberry/i', $u_agent)) {
            $operating_system = 'Blackberry';
        } elseif (preg_match('/webos/i', $u_agent)) {
            $operating_system = 'Mobile';
        }
    } else {
        $operating_system = php_uname('s');
    }

    return $operating_system;
}
$operating_system = get_operating_system();

session_start();
require ("../app/db/base.php");
if(isset($_SESSION['login_email'])!="")
{header("Location: dashboard.php");}
if(isset($_POST['btn-login']))
{   $user_email = trim($_POST['user_email']);
    $user_password = trim($_POST['password']);
    try
    {	$stmt = $db_con->prepare("SELECT u.*,c.* FROM users u,company c WHERE u.username=:username and u.company_id=c.company_id and u.section_id=c.section_id ");
        $stmt->execute(array(":username"=>$user_email));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        $stored_hashed_password = $row['passwords']; //
        if (password_verify($user_password, $stored_hashed_password)) {
            $_SESSION['login_email'] = $row['username'];
            $_SESSION['companyid']= $row['company_id'];
            $_SESSION['sectionid']= $row['section_id'];
            $_SESSION["userid"] = $row['user_id'];
            $_SESSION["PBI_ID"] = $row['PBI_ID'];
            $_SESSION["username"] = $row['fname'];
            $_SESSION["email"] = $row['email'];
            $_SESSION["warehouse"] = $row['warehouse_id'];
            $_SESSION["department"]= $row['department'];
            $_SESSION["dep_power_level"]= $row['dep_power_level'];
            $_SESSION["userlevel"]= $row['level'];
            $_SESSION["language"] = 'English';
            $_SESSION["logo_color"]= $row['logo_color'];
            $_SESSION["designation"]= $row['designation'];
            $_SESSION["status"]= $row['status'];
//$_SESSION["PASSCODE"]= $row[password];
            $_SESSION['usergroup']=$row['group_for'];
            $_SESSION['gander']=$row['gander'];
            $_SESSION['userpic']=$row['picture_url'];
            $_SESSION['create_date']=date('Y-m-d');
            $res=mysqli_query($conn, "SELECT * FROM company WHERE  section_id='".$_SESSION['sectionid']."' and company_id='".$_SESSION['companyid']."'");
            $userRow=mysqli_fetch_array($res);
            $_SESSION['company_name']=$userRow['company_name'];
            $_SESSION['company_address']=$userRow['address'];
            $_SESSION['com_short_name']=$userRow['com_short_name'];
            $_SESSION['section_name']=$userRow['section_name'];
            $_SESSION['aToken']=$key;
            $login_activity_insert = mysqli_query($conn, "INSERT INTO user_activity_log (user_id,ip,access_time,browser,access_status,os,access_token)
VALUES ('".$row['user_id']."','$ip','".$access_time."','".$browser."','success','".$operating_system."','".$key."')");
            header("Location: dashboard.php");
        } else{
            $login_activity_insert = mysqli_query($conn, "INSERT INTO user_activity_log (user_id,ip,access_time,browser,access_status,os)
VALUES ('".$row['user_id']."','$ip','".$access_time."','".$browser."','decline','".$operating_system."')");
            $message = 'email or password does not exist.';
        }}
    catch(PDOException $e){
        echo $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Software Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
            color: #444;
        }

        .login-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            font-size: 1rem;
            color: #777;
        }

        .login-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.6rem;
            color: #555;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus {
            border-color: #4ca1af;
            outline: none;
            box-shadow: 0 0 6px rgba(76, 161, 175, 0.5);
        }

        .login-button {
            background-color: #4ca1af;
            color: white;
            font-size: 1rem;
            padding: 0.9rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .login-button:hover {
            background-color: #3b8d99;
            transform: translateY(-2px);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }

        .forgot-password a {
            text-decoration: none;
            color: #4ca1af;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .brand-logo {
            display: block;
            margin: 0 auto 1.5rem auto;
            width: 80px;
            height: 80px;
        }

    </style>
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <img src="http://icpd.icpbd-erp.com/4400542.png" alt="ERP Logo" class="brand-logo">
        <h1>Welcome to ERP</h1>
        <p>Please log in to access your account.</p>
        <?php if(isset($message)){ ?>
            <br>
            <p style="color: red"><?=$message?></p>
        <?php } ?>
    </div>
    <form class="login-form" action="" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="user_email" name="user_email" placeholder="Enter your username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="login-button" name="btn-login">Login</button>
    </form>
    <div class="forgot-password">
        <a href="#">Forgot your password?</a>
    </div>
</div>
</body>
</html>
