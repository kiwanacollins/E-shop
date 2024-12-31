<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['email']) || !isset($_REQUEST['token'])) {
    header('location: login.php');
    exit;
}

// Check if email and token are valid
$statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=? AND cust_token=?");
$statement->execute(array($_REQUEST['email'],$_REQUEST['token']));
$total = $statement->rowCount();
if($total == 0) {
    header('location: login.php');
    exit;
}

if(isset($_POST['form1'])) {
    $valid = 1;

    if(empty($_POST['cust_password']) || empty($_POST['cust_re_password'])) {
        $valid = 0;
        $error_message .= "Password can not be empty<br>";
    }

    if(!empty($_POST['cust_password']) && !empty($_POST['cust_re_password'])) {
        if($_POST['cust_password'] != $_POST['cust_re_password']) {
            $valid = 0;
            $error_message .= "Passwords do not match<br>";
        }
    }

    if($valid == 1) {
        // Update password and remove the token
        $password = md5($_POST['cust_password']);
        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_password=?, cust_token=? WHERE cust_email=? AND cust_token=?");
        $statement->execute(array($password,'',strip_tags($_REQUEST['email']),strip_tags($_REQUEST['token'])));
        
        header('location: login.php?reset=success');
        exit;
    }
}
?>

<div class="page-banner">
    <div class="inner">
        <h1>Reset Password</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <?php
                                if($error_message != '') {
                                    echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                                }
                                ?>
                                <div class="form-group">
                                    <label for="">New Password *</label>
                                    <input type="password" class="form-control" name="cust_password">
                                </div>
                                <div class="form-group">
                                    <label for="">Confirm Password *</label>
                                    <input type="password" class="form-control" name="cust_re_password">
                                </div>
                                <div class="form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-primary" value="Update Password" name="form1">
                                </div>
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>