<?php
use FTP\Connection;
require_once "dashboard.php";
require_once "../src/User.php";
require_once "../src/Constants.php";
require_once "../src/EditAccountControl.php";

class EditAccountForm extends Dashboard
{
    
    public static function Error($ErrorCode)
    {
        //check the error code, display appropriate page
        switch($ErrorCode) 
        {
            case Constants::$INVALID_SESSION:
                header("Location: ../public/LoginForm.php");
                break;
            case Constants::$UNAUTHORIZED:
                header("Location: ../public/UserSearchForm.php");
                break;
            default: //everything else incl. invalid input and edit failed error codes
                header("Location: ../public/EditAccountForm.php");
                break;
        }
    }
    public static function submit()
    {
        $User = new User(); //create user object

        /*Get information from the post request*/
        $User->SetEmail(strtolower($_POST['email'])); //is converted to lower
        $User->SetAccType($_POST['acctype']);
        $User->SetPassword($_POST['password']);
        $User->SetFName($_POST['fname']);
        $User->SetLName($_POST['lname']);
        $User->SetDOB($_POST['dob']); //is already UTC
        $User->SetYear($_POST['studentyear']); //only if student
        $User->SetRank($_POST['facultyrank']);  //only if faculty, ensure null otherwise
        $User->SetSQuestion($_POST['squestion']);
        $User->SetSAnswer($_POST['sanswer']);
        $User->SetPrevEmail($_POST['prevemail']);

        if($_POST['acctype'] == null)
        {
            throw new Exception("input did not exist");
        }
        else
        {
            /*Checking studentyear and facultyrank*/
            if ($User->GetAccType() == Constants::$STUDENT_TYPE) {
                $User->SetRank(null);
            } else if ($User->GetAccType() == Constants::$FACULTY_TYPE) {
                $User->SetYear(null);
            }
        }

        //edit user
        EditAccountControl::submitUser($User);
    }

}

if ($_SERVER["REQUEST_METHOD"] == "POST") //if POST request detected
{
    //check if edit form submitted with account type
    if(isset($_POST["acctype"]))
    {
        EditAccountForm::submit(); //call submit for user search
    }
}
else{
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if ("edit" == parse_url($url, PHP_URL_QUERY))
    {
        $prevemail = $_COOKIE['prevemail']; //get the previous email (username searched for)
        $userinfo = array(); //array to hold user data if found

        if (isset($_COOKIE['acctype'])) {

            /*Get user information from the cookie
              $userinfo[0] left blank - pos for user id - not used
            */
            array_push($userinfo, "", $_COOKIE['email'], $_COOKIE['password'], $_COOKIE['fname'], $_COOKIE['lname'], 
                    $_COOKIE['dob'], $_COOKIE['studentyear'], $_COOKIE['facultyrank'], $_COOKIE['squestion'], 
                    $_COOKIE['sanswer'], $_COOKIE['prevemail'], $_COOKIE['acctype']);

            // user was found
            $error = false;
        }
        else
        {
            // user was not found
            $error = true;
        }
    }
    else
    {
        $error = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../resources/secure_app.css">
    <link rel="icon" type="image/svg" href="../resources/Header_Lock_Image.svg">
    <script async src="../resources/nav.js"></script>
    <meta charset="utf-8" />
    <title>Secure ED. - Edit Account</title>
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
            <h1>Edit Account</h1>
            <div class=horizontal_line>
                <hr>
            </div>

            <?php
                if ($error)
                {
                    echo "An error has occurred finding user";
                    echo "$prevemail";
                }
                if(!$userinfo)
                {
                    echo "An error has occurred obtaining user info.";
                }
            ?>

            <p id="submiterror" style="display:none"></p>

            <br><br>

            <div style="text-align:center">
                <div class = "edit_acc_pane">
                    <form action="../public/EditAccountForm.php" method="POST" id="accform">
                        <label class="edit_acc_label">Account type:</label>
                        <select name="acctype" id="acctype" onchange="swapselection()">
                            <option value="2" <?php if($userinfo[11] == 2){echo "selected";} ?> ">Faculty</option>
                            <option value="3" <?php if($userinfo[11] == 3){echo "selected";} ?> ">Student</option>
                        </select>
                    <div class=horizontal_line>
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
                                    <input type="text" id="fname" name="fname" value="<?php if(!$error){echo "$userinfo[3]";} ?>">
                                </td>

                                <!--Last Name-->
                                <td>
                                    <label class = "edit_acc_label"> Last Name: </label>
                                </td>
                                <td>
                                    <input type="text" id="lname" name="lname" value="<?php if(!$error){echo "$userinfo[4]";} ?>">
                                </td>
                            </tr>

                            <tr>
                                <!--Date of Birth-->
                                <td>
                                    <label class = "edit_acc_label"> Date of Birth: </label>
                                </td>
                                <td>
                                    <input type="date" id="dob" name="dob" value="<?php if(!$error){echo $userinfo[5];} ?>">
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
                                    <label class = "edit_acc_label" id="positionlabel"> <?php if($userinfo[11] == 3){echo "Year:";} else {echo "Rank:";}?> </label>
                                </td>
                                <td>
                                    <select name="studentyear" id="studentyear" style = "<?php if($userinfo[11] != 3){echo "display:none";}?>">
                                        <optgroup label="Student">
                                            <option value="1" <?php if($userinfo[6] == 1){echo "selected";} ?>>Freshman</option>
                                            <option value="2" <?php if($userinfo[6] == 2){echo "selected";} ?>>Sophomore</option>
                                            <option value="3" <?php if($userinfo[6] == 3){echo "selected";} ?>>Junior</option>
                                            <option value="4" <?php if($userinfo[6] == 4){echo "selected";} ?>>Senior</option>
                                        </optgroup>
                                    </select>
                                    <select name="facultyrank" id="facultyrank" style = "<?php if($userinfo[11]!==2){echo "display:none";}?>">
                                        <optgroup label="Faculty">
                                            <option value="Instructor" <?php if($userinfo[7] === "Instructor"){echo "selected";} ?>>Instructor</option>
                                            <option value="Adjunct" <?php if($userinfo[7] === "Adjunct"){echo "selected";} ?>>Adjunct Professor</option>
                                            <option value="Assistant" <?php if($userinfo[7] === "Assistant"){echo "selected";} ?>>Assistant Professor</option>
                                            <option value="Associate" <?php if($userinfo[7] === "Associate"){echo "selected";} ?>>Associate Professor</option>
                                            <option value="Professor" <?php if($userinfo[7] === "Professor"){echo "selected";} ?>>Professor</option>
                                            <option value="Emeritus" <?php if($userinfo[7] === "Emeritus"){echo "selected";} ?>>Professor Emeritus</option>
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
                                    <input type="email" name="email" id="email" value="<?php if(!$error){echo "$userinfo[1]";} ?>">
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
                                    <input type="email" name="confirmemail" id="confirmemail" value="<?php if(!$error){echo $userinfo[1];} ?>">
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
                                    <input type="text" name="squestion" value="<?php if(!$error){echo "$userinfo[8]";} ?>">
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
                                    <input type="text" name="sanswer" value="<?php if(!$error){echo "$userinfo[9]";} ?>">
                                </td>

                                <!--Blank-->
                                <td>
                                    <input type="hidden" name="prevemail" value="<?php echo "$prevemail" ?>">
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
                    <input type="button" value="Cancel" onclick=" location.href = 'UserSearchForm.php'">
                </div>
            </div>
        </main>
    </div>
    <script async src = "../resources/SelectionAndSubmitDisplay.js"></script>
</body>
</html>
