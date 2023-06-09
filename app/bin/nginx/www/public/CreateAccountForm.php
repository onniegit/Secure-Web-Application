<?php
require_once "dashboard.php";
require_once "../src/CreateAccountControl.php";
class CreateAccountForm extends Dashboard
{
    public static function Error($ErrorCode)
    {
        //check the error code, display appropriate page
        switch($ErrorCode) 
        {
            case Constants::$INVALID_SESSION:
                header("Location: ../public/LoginForm.php");
                break;
            case Constants::$INVALID_INPUT:
                header("Location: ../public/CreateAccountForm.php?input=fail");
                break;
            default:
        }
    }

    public static function submit()
    {
        /*Get information from the post request*/
        $Email = strtolower($_POST['email']); //is converted to lower
        $AccType = $_POST['acctype'];
        $Password = $_POST['password'];
        $FName = $_POST['fname'];
        $LName = $_POST['lname'];
        $DOB = $_POST['dob']; //is already UTC
        $Year = $_POST['studentyear']; //only if student, ensure null otherwise (must be a number)
        $Rank = $_POST['facultyrank'];  //only if faculty, ensure null otherwise
        $SQuestion = $_POST['squestion'];
        $SAnswer = $_POST['sanswer'];

        if($AccType == null)
            throw new Exception("input did not exist");
        else
        {
            if ($AccType == Constants::$STUDENT_TYPE) 
                $Rank = null; 
            else if ($AccType == Constants::$FACULTY_TYPE) 
                $Year = null;
         }

         $data = array($Email, $Password, $AccType, $FName, $LName, $DOB, $Year, $Rank, $SQuestion, $SAnswer, $Email);

        //create account
        CreateAccountControl::submit($data);
    }
}
error_log("loading page", 0);

CreateAccountForm::LoadPage();

if ($_SERVER["REQUEST_METHOD"] == "POST") //if POST request detected
{
    CreateAccountForm::Submit(); //call submit
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../resources/secure_app.css">
    <link rel="icon" type="image/svg" href="../resources/Header_Lock_Image.svg">
    <script async src="../resources/nav.js"></script>
    <meta charset="utf-8" />
    <title>Secure ED. - Create Account</title>
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

        <!--Navigation Buttons-->
        <nav>
            <button class="button_large" type="button" onclick="location.href = 'AdminDashboard.php'">Dashboard</button>
            <button class="button_large" type="button" onclick="Logout();">Log Out</button>
        </nav>

        <main>

            <!--Heading-->
            <h1>Create Account</h1>
            <div class="horizontal_line">
                <hr>
            </div>

            <p id="submiterror" style="display:none"></p>

            <div style="text-align:center">
                <div class = "edit_acc_pane">
                <?php
                $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if ("input=fail" == parse_url($url, PHP_URL_QUERY))
                {
                    echo "input is invalid.";
                }
                ?>
                    <form action="CreateAccountForm.php" method="POST" id="accform">
                        <label class="edit_acc_label">Account type:</label>
                        <select name="acctype" id="acctype" onchange="swapselection()">
                                <optgroup label="Choose one">
                                    <option selected="selected" value="2" >Faculty</option>
                                    <option value="3">Student</option>
                                </optgroup>
                        </select>
                    <div class="horizontal_line">
                        <hr>
                    </div>
                    <!--Input boxes-->
                        <table>
                            <tbody>
                            <tr>
                                <!--First Name-->
                                <td>
                                    <label class = "edit_acc_label"> First Name: </label>
                                </td>
                                <td>
                                    <input type="text" id="fname" name="fname" value="">
                                </td>

                                <!--Last Name-->
                                <td>
                                    <label class = "edit_acc_label"> Last Name: </label>
                                </td>
                                <td>
                                    <input type="text" id="lname" name="lname" value="">
                                </td>
                            </tr>

                            <tr>
                                <!--Date of Birth-->
                                <td>
                                    <label class = "edit_acc_label"> Date of Birth: </label>
                                </td>
                                <td>
                                    <input type="date" id="dob" name="dob" value="">
                                </td>

                                <!--Blank-->
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <!--Faculty Rank/Student Year-->
                                <td>
                                    <label class = "edit_acc_label" id="positionlabel"> Rank: </label>
                                </td>
                                <td>
                                    <select name="studentyear" id="studentyear" style = "display:none;">
                                        <optgroup label="Student">
                                            <option selected="selected" value="1" >Freshman</option>
                                            <option value="2" >Sophomore</option>
                                            <option value="3" >Junior</option>
                                            <option value="4" >Senior</option>
                                        </optgroup>
                                    </select>
                                    <select name="facultyrank" id="facultyrank" style = "display:block;">
                                        <optgroup label="Faculty">
                                            <option selected="selected" value="Instructor" >Instructor</option>
                                            <option value="Adjunct" >Adjunct Professor</option>
                                            <option value="Assistant" >Assistant Professor</option>
                                            <option value="Associate" >Associate Professor</option>
                                            <option value="Professor">Professor</option>
                                            <option value="Emeritus">Professor Emeritus</option>
                                        </optgroup>
                                    </select>
                                </td>

                                <!--Blank-->
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr> <!--Blank for spacing-->
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <!--Email-->
                                <td>
                                    <label class = "edit_acc_label"> Email: </label>
                                </td>
                                <td>
                                    <input type="email" name="email" id="email" value="">
                                </td>

                                <!--Blank-->
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <!--Confirm Email-->
                                <td>
                                    <label class = "edit_acc_label"> Confirm Email: </label>
                                </td>
                                <td>
                                    <input type="email" name="confirmemail" id="confirmemail" value="">
                                </td>

                                <!--Blank-->
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <!--Blank for spacing-->
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <!--Password-->
                                <td>
                                    <label class = "edit_acc_label"> Password: </label>
                                </td>
                                <td>
                                    <input type="password" name="password" id="password" value="">
                                </td>

                                <!--Blank-->
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <!--Confirm Password-->
                                <td>
                                    <label class = "edit_acc_label"> Confirm Password: </label>
                                </td>
                                <td>
                                    <input type="password" name="confirmpassword" id="confirmpassword" value="">
                                </td>

                                <!--Blank-->
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <!--Blank for spacing-->
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <!--Security question-->
                                <td>
                                    <label class = "edit_acc_label"> Security Question: </label>
                                </td>
                                <td>
                                    <input type="text" name="squestion" value="">
                                </td>

                                <!--Blank-->
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <!--Answer-->
                                <td>
                                    <label class = "edit_acc_label"> Answer: </label>
                                </td>
                                <td>
                                    <input type="text" name="sanswer" value="">
                                </td>

                                <!--Blank-->
                                <td>
                                </td>
                                <td>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>

                <div style="text-align: left;">
                    <input type="submit" value="Submit" onclick="submitAccount()">&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Cancel" onclick=" location.href = 'AdminDashboard.php'">
                </div>
            </div>
        </main>
    </div>
    <script src = "../resources/SelectionAndSubmitDisplay.js"></script>
</body>
</html>