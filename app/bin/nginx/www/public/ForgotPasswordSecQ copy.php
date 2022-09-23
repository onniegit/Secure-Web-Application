<?php
// resume the session
session_start();

try {
  require_once "../src/RedirectController.php";

  if ($GLOBALS['redirectController']->ValidateLogin()) 
  {
    //redirect to dashboard
    header("Location: dashboard.php");
  } 
 
}
catch(Exception $e)
{
    header("Location: LoginForm.php?login=fail");
}

//Get user's security question - now done within the controller
require_once "../src/ForgotPwController.php";
$secquestion = ForgotPwController::getSecQ($_SESSION['email']);
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


            <div class = "SecurityQuestion" style="text-align:center">
                <p><?php echo $secquestion;?></p>
                <?php
                                $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                   if("answercheck=fail" == parse_url($url, PHP_URL_QUERY))
                            {
                            echo "The answer is invalid";
                            }
                ?>

                <form action="../src/ForgotPasswordSecQLogic.php" method="POST">
                    <label for="Answer">Answer:&nbsp;&nbsp;</label>
                    <input type="text" id="Answer" name="Answer"><br><br>
                    <input type="submit" value="Submit">
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Cancel" onclick=" location.href = 'LoginForm.php'">
                </form>
            </div>
        </main>
    </div>
</body>
</html>