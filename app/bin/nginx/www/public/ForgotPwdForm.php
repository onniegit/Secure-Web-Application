<?php
require_once "../src/ForgotPwdControl.php"; 
require_once "../src/Constants.php";

class ForgotPwdForm
{
  public static function Submit()
  {
    $username = strtolower($_POST['email']);
    $data = array($username);
    
    ForgotPwController::ForgotPassword($data, Constants::$USERNAME_TYPE);
  }

  public static function Error($ErrorCode)
  {
    //check the error code
    switch($ErrorCode) 
    {
      case Constants::$INVALID_CREDENTIALS:
        header("Location: ../public/ForgotPwdForm.php?login=fail");
        break;
      case Constants::$INVALID_INPUT:
        header("Location: ../public/ForgotPwdForm.php?input=fail");
        break;
      default:
    }
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") //if POST request detected
{
    ForgotPwdForm::Submit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../resources/secure_app.css">
    <link rel="icon" type="image/svg" href="../resources/Header_Lock_Image.svg">
    <meta charset="utf-8" />
    <title>Secure ED. - Forgot Password</title>
</head>

<body>
<div id="wrapper">
    <header>
        <table class="header_table">
            <tbody>
            <tr>
                <td class="lock"><img src="../resources/Header_Lock_Image.svg" style="width:9vh;" alt="Header_lock"></td>
                <td class="title"><b>Secure ED.</b></td>
                <td class="header_table_cell"></td>
            </tr>
            </tbody>
        </table>
    </header>

    <main>

        <!--Heading-->
        <h1>Forgot Password</h1>
        <div class=horizontal_line>
            <hr>
        </div>

        <div id =ForgotPasswordContent style="text-align:center">
            <?php
            //check url to see if emailcheck failed
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            if ("emailcheck=fail" == parse_url($url, PHP_URL_QUERY))
            {
                echo "The email is invalid.";
            }
            ?>
            <div class="spacer">Please enter your email:</div>
            <form action="../public/ForgotPwdForm.php" method="POST">
                <label for="email">Email:&nbsp;&nbsp;</label>
                <input type="text" id="email" name="email"> <br>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" id="submit" value ="Submit">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" value="Cancel" onclick=" location.href = 'LoginForm.php'">
            </form>
        </div>
    </main>
</div>
</body>