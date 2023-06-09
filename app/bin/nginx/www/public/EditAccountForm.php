<?php
require_once "dashboard.php";
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
            case Constants::$INVALID_INPUT:
                header("Location: ../public/EditAccountForm.php?input");
                break;
            default: //everything else incl. edit failed error codes
                header("Location: ../public/EditAccountForm.php");
                break;
        }
    }
    public static function submit()
    {
        /*Get information from the post request*/
        $Email = strtolower($_POST["email"]); //is converted to lower
        $AccType = $_POST["acctype"];
        $Password = $_POST["password"];
        $FName = $_POST["fname"];
        $LName = $_POST["lname"];
        $DOB = $_POST["dob"]; //is already UTC
        $Year = $_POST["studentyear"]; //only if student
        $Rank = $_POST["facultyrank"];  //only if faculty, ensure null otherwise
        $SQuestion = $_POST['squestion'];
        $SAnswer = $_POST['sanswer'];
        $Prevemail = $_POST['prevemail'];

        if($AccType == null)
            throw new Exception("input did not exist");
        else
        {
            if ($AccType == Constants::$STUDENT_TYPE) 
                $Rank = null; 
            else if ($AccType == Constants::$FACULTY_TYPE) 
                $Year = null;
         }

         $userdata = array($Email, $Password, $AccType, $FName, $LName, $DOB, $Year, $Rank, $SQuestion, $SAnswer, $Prevemail);

        //error_log("submitting user", 0);
         //edit user
        EditAccountControl::submitUser($userdata);
    }

}

if ($_SERVER["REQUEST_METHOD"] == "POST") //if POST request detected
{
    error_log("found post", 0);

    //check if edit form submitted with account type
    if(isset($_POST["acctype"]))
    {
        error_log("calling edit account submit", 0);
        EditAccountForm::submit(); //call submit for user search
    }
}
else
{
    //error_log("i see get", 0);
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    $userinfo = array(); //array to hold user data if found

    //error_log("setting error 0", 0);
    $error = Constants::$NONE;

    if (isset($_COOKIE['acctype']))
    {
        //error_log("acctype is set...", 0);
        /*Get user information from the cookie
        $userinfo[0] left blank - pos for user id - not used
       */
        array_push($userinfo, "", $_COOKIE['email'], $_COOKIE['password'], $_COOKIE['fname'], $_COOKIE['lname'], 
                    $_COOKIE['dob'], $_COOKIE['studentyear'], $_COOKIE['facultyrank'], $_COOKIE['squestion'], 
                    $_COOKIE['sanswer'], $_COOKIE['prevemail'], $_COOKIE['acctype']);       
    }
    else
    {
        if ("edit" == parse_url($url, PHP_URL_QUERY))
        {
            // user was not found
            error_log("setting error 6", 0);
            $error = Constants::$USER_NOT_FOUND;
        }
    }
    
    if ("input" == parse_url($url, PHP_URL_QUERY))
    {
        //error_log("setting error 2", 0);
        $error = Constants::$INVALID_INPUT;
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
                if ($error == 6)
                {
                    echo "An error has occurred finding user.";
                }
                else if($error == 2)
                {
                    echo "Invalid input.";
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
                                    <input type="text" id="fname" name="fname" value="<?php if($error == 0){echo "$userinfo[3]";} ?>">
                                </td>

                                <!--Last Name-->
                                <td>
                                    <label class = "edit_acc_label"> Last Name: </label>
                                </td>
                                <td>
                                    <input type="text" id="lname" name="lname" value="<?php if($error == 0){echo "$userinfo[4]";} ?>">
                                </td>
                            </tr>

                            <tr>
                                <!--Date of Birth-->
                                <td>
                                    <label class = "edit_acc_label"> Date of Birth: </label>
                                </td>
                                <td>
                                    <input type="date" id="dob" name="dob" value="<?php if($error == 0){echo $userinfo[5];} ?>">
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
                                    <input type="email" name="email" id="email" value="<?php if($error == 0){echo "$userinfo[1]";} ?>">
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
                                    <input type="email" name="confirmemail" id="confirmemail" value="<?php if($error == 0){echo $userinfo[1];} ?>">
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
                                    <input type="text" name="squestion" value="<?php if($error == 0){echo "$userinfo[8]";} ?>">
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
                                    <input type="text" name="sanswer" value="<?php if($error == 0){echo "$userinfo[9]";} ?>">
                                </td>

                                <!--Blank-->
                                <td>
                                    <!--<input type="hidden" name="prevemail" value="<--><!--?php echo "$prevemail" ?>">-->
                                    <input type="hidden" name="prevemail" value="<?php if($error == 0){echo "$userinfo[10]";}?>">
                                </td>
                                <td>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>

                <div style="text-align: left;">
                    <!-- <input type="submit" value="Submit" onclick="submitAccount()">&nbsp;&nbsp;&nbsp;&nbsp; -->
                    <input type="submit" value="Submit" onclick="submitAccount()">&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Cancel" onclick=" location.href = 'UserSearchForm.php'">
                </div>
            </div>
        </main>
    </div>
    <!-- <script async src = "../resources/SelectionAndSubmitDisplay.js"></script> -->
    <script>
        function swapselection() //changes the value entry based on faculty or student
        {
            //get elements from page
            var studentselect = document.getElementById("studentyear");
            var facultyselect = document.getElementById("facultyrank");
            var acctype = document.getElementById("acctype");
            var positionlabel = document.getElementById("positionlabel");

            //change parts of page depending on student or faculty
            if(acctype.options[acctype.selectedIndex].text === "Faculty")
            {
            studentselect.style.display = "none";
            facultyselect.style.display = "inline";
            positionlabel.innerText = "Rank:";
            }
            else
            {
            studentselect.style.display = "inline";
            facultyselect.style.display = "none";
            positionlabel.innerText = "Year:";
            }
        }

        function submitAccount() //checks for basic errors when submitting
        {
            //get elements from page
            var pass = document.getElementById("password");
            var confirmpass = document.getElementById("confirmpassword");
            var email = document.getElementById("email");
            var confirmemail = document.getElementById("confirmemail");
            var submiterror = document.getElementById("submiterror");
            var accform = document.getElementById("accform");
            var cansubmit = true;

            try
            {
                while (submiterror.removeChild(submiterror.childNodes[0]) !== null)
                {
                //tries to remove all previous error messages if they exist
                }
            }
            catch
            {
                //succeeds when it throws exception
            }

            //reset the element for errors to a default state
            submiterror.innerText = "";
            submiterror.style.display = "none";

            //check if pass is empty
            if(pass.value === "")
            {
                cansubmit = false;
                submiterror.innerText = "Password is empty. \n";
                submiterror.style.display = "block";
            }
            //check if pass and confirm pass are not the same
            if (pass.value !== confirmpass.value)
            {
                cansubmit = false;
                submiterror.innerText = submiterror.innerText.concat("Password and Confirm Password are not the same. \n");
                submiterror.style.display = "block";
            }
            //check if email is empty
            if(email.value === "")
            {
                cansubmit = false;
                submiterror.innerText = submiterror.innerText.concat("Email is empty. \n");
                submiterror.style.display = "block";
            }
            //check if email and confirmemail are not the same
            if (email.value !== confirmemail.value)
            {
                cansubmit = false;
                submiterror.innerText = submiterror.innerText.concat("Email and Confirm Email are not the same. \n");
                submiterror.style.display = "block";
            }
            if(cansubmit)
            {
                accform.submit();
            }
        }
    </script>
</body>
</html>
