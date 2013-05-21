<?

require_once('lib.php');

if (register_post_keys('email', 'password')) {
    $user_id = $db->recruiter_login($email, $password);
    if ($user_id) {
        login_recruiter($user_id);
    } else {
        $error = 'Login failed';
    }
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="login.css" type="text/css"/>
  </head>
  <body><form action="recruiter_login.php" method="POST">
    <? include('error.php'); ?>
    <h1>
      Recruiter Sign In
    </h1>
    <table class="box">
      <tr>
        <td>
          Email:
        </td>
        <td>
          <input type="text"
                 class="loginEmailField"
                 name="email"
                 value="<? echo $email; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          Password:
        </td>
        <td>
          <input type="password"
                 class="loginPasswordField"
                 name="password" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="submitCell">
          <input type="submit" value="Sign In" class="btn" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="signup">
          <a href="recruiter_signup.php">Sign up now</a>
        </td>
      </tr>
    </table>
  </form></body>
</html>

