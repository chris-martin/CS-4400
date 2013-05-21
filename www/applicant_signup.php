<?php

require_once('lib.php');

if (register_post_keys('name', 'email', 'password', 'password_retype',
                       'phone', 'degree', 'birth', 'experience',
                       'citizenship', 'description')) {

    $error = array();

    if ($password != $password_retype) {
        $error[] = "Passwords do not match.";
    }

    if ($db->customer_email_exists($email)) {
        $error[] = "The specified email is already in use.";
    }

    if (count($error) == 0) {

        $user_id = $db->create_applicant($password, $email, $name, $phone,
                    $degree, $experience, $citizenship, $birth, $description);

        if ($user_id) {
            login_applicant($user_id);
        } else {
            $error[] = "Failed to add user.";
        }

    }

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="signup.css" type="text/css"/>
  </head>
  <body><form action="applicant_signup.php" method="POST">
    <? include('error.php'); ?>
    <h1>
      Create an Applicant Account
    </h1>
    <table class="box">
      <tr>
        <td>
          * Name
        </td>
        <td>
          <input type="text" name="name" />
        </td>
      </tr>
      <tr>
        <td>
          * Email
        </td>
        <td>
          <input type="text" name="email" />
        </td>
      </tr>
      <tr>
        <td>
          * Choose a password
        </td>
        <td>
          <input type="password" name="password" />
        </td>
      </tr>
      <tr>
        <td>
          * Re-enter password
        </td>
        <td>
          <input type="password" name="password_retype" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="profileHeader">
          Your Profile (You can update it later)
        </td>
      </tr>
      <tr>
        <td>
          Phone
        </td>
        <td>
          <input type="text" name="phone" maxlength="10" />
        </td>
      </tr>
      <tr>
        <td>
          Highest degree
        </td>
        <td>
          <select name="degree">
          <? foreach ($db->lookup_degree() as $id => $name) { ?>
            <option value="<? echo "$id"; ?>">
              <? echo $name; ?>
            </option>
          <? } ?>
            <option value="" selected="selected"></option>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Birth year
        </td>
        <td>
          <select name="birth">
          <? for ($i = date("Y") - 123; $i <= date("Y"); $i++) { ?>
            <option value="<? echo $i ?>">
              <? echo $i ?>
            </option>
          <? } ?>
            <option value="" selected="selected"></option>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Years of experience
        </td>
        <td>
          <select name="experience">
          <? for ($i = 80; $i >= 0; $i--) { ?>
            <option value="<? echo $i ?>">
              <? echo $i ?>
            </option>
          <? } ?>
            <option value="" selected="selected"></option>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Citizenship
        </td>
        <td>
          <select name="citizenship">
          <? foreach ($db->lookup_citizenship() as $id => $name) { ?>
            <option value="<? echo "$id"; ?>">
              <? echo $name; ?>
            </option>
          <? } ?>
            <option value="" selected="selected"></option>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Short description<br />(Not to exceed<br />500 characters)
        </td>
        <td>
          <textarea name="description"></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="submitCell">
          <input type="Submit" value="Submit" class="btn" />
        </td>
      </tr>
    </table>
  </form></body>
</html>

