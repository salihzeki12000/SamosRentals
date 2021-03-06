<?php
include 'admin/configuration.php';
session_start();

//Check if already logged
if (islogged()) {
    die("<script>window.history.back()</script>");
}

//Check if Method is POST and authenticate
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Gather Data from post
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    //Authenticate person
    authenticate();
}

function authenticate()
{
    global $username, $password;

    //Connect
    $con = db_connect();
    if ($con->connect_errno) {
        return;
    }
    // SQL query with prepared statement(sql injections) to fetch information of registerd users and finds user match.
    $sql = $con->prepare('SELECT * FROM user WHERE Password = ? AND Username= ? AND Active=1');
    $sql->bind_param('ss', $password, $username);
    $sql->execute();

    $result = $sql->get_result();
    $num_row = mysqli_num_rows($result);
    if ($num_row == 1) {

        // Store Session Data
        $row = mysqli_fetch_array($result);

        $_SESSION['userid'] = $row['Username'];
        $_SESSION['role'] = $row['Role'];
        $_SESSION['fname'] = $row['FirstName'];
        $_SESSION['lname'] = $row['LastName'];
        $_SESSION['avatar'] = $row['Image'];
        $_SESSION['start'] = time(); // Taking now logged in time.
        $_SESSION['expire'] = $_SESSION['start'] + (30 * 60);

        header("Location: index.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <?php
    $Page_Title = "Σύνδεση";
    include 'head.php';
    ?>

    <style>
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        }

        ::-webkit-scrollbar-thumb {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
        }

        .login-form {
            background-color: #fafafa;
            margin: 0 auto 3rem;
            border-radius: 5px;
            padding: 1.5rem 2rem 0.5rem;
        }

        .login-form #login_btn {
            margin: 0 auto 2rem;
        }

        .Login {
            position: absolute;
            width: 100%;
            height: 91%;
            text-align: center;
        }

        .Login .col {
            display: inline-block;
            float: none;
        }

        .Login hr {
            margin: 4rem 0 3rem;
            border: 0;
            height: 3px;
            box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        }

        .Login h1 {
            color: #fff;
            text-align: center;
            margin-top: 5%;
            font-size: 3.5rem;
        }

        .Login p {
            color: #fff;
            text-align: center;
            margin-top: 0;
        }

        .Login #register_btn {
            margin: 0 auto 2rem;
        }
    </style>
</head>
<body class="light-blue">

<!--Navigation Menu-->
<?php
include 'header.php';
?>
<!--Navigation Menu-->

<!-- Φόρμα σύνδεσης -->
<div class="row Login">
    <!-- Logo text -->
    <div class="row">
        <div class="col l6">
            <h1 class="animated zoomInDown">Σύνδεση</h1>
        </div>
    </div>
    <!-- Logo text -->

    <div class="col center">
        <div class="animated tada login-form z-depth-3">
            <form action="login.php" autocomplete="on" method="POST">
                <div class="input-field">
                    <i class="mdi-action-account-circle prefix"></i>
                    <input type="text" id="username" name="username" maxlength="30"
                           value="<?php if (!empty($username)) echo $username; ?>" required>
                    <label for="username">Όνομα χρήστη</label>
                </div>
                <div class="input-field">
                    <i class="mdi-communication-vpn-key prefix"></i>
                    <input type="password" id="password" name="password" maxlength="30" required>
                    <label for="password">Κωδικός χρήστη</label>
                </div>
                <div class="center-btn">
                    <button type="submit" class="waves-effect waves-light green btn" id="login_btn"><i
                            class="mdi-content-send right"></i>Εισοδος
                    </button>
                    <a class="waves-effect waves-light btn light-blue" id="register_btn" href="register.php"><i
                            class="mdi-social-person-add left"></i>Εγγραφη ως νεος Χρηστης</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer text -->
    <div class="row">
        <div class="col l5 m8 s12">
            <p class="animated fadeInUp flow-text"><b>Ψάχνεις δωμάτιο?</b> Συνδέσου τώρα στο <b>μεγαλύτερο</b> και <b>πληρέστερο</b>
                δίκτυο ενοικιαζόμενων δωματίων του νησιού! Εδώ θα βρείς περισσότερα από <b>100+</b> δωμάτια και
                ενοικιαζόμενα διαμερίσματα!</p>
        </div>
    </div>
    <!-- Footer text -->

</div>

</body>
</html>