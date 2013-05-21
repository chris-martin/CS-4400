<?

require_once('lib.php');

if (register_post_keys('password')) {
    $user_id = $db->admin_login($password);
    if ($user_id) {
        login_admin($user_id);
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
  <body><form action="admin_login.php" method="POST">
    <h1>
      Administrator Sign In
    </h1>
    <? include('error.php'); ?>
    <table class="box">
      <tr>
        <td>
          Password:
        </td>
        <td>
          <input type="password" class="loginPasswordField" name="password" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="submitCell">
          <input type="submit" value="Sign In" class="btn" />
        </td>
      </tr>
    </table>
  </form></body>
</html>

