<?

require_once('lib.php');

if (register_post_keys('company_name', 'contact_person',
                       'contact_email', 'password', 'password_retype',
                       'phone', 'fax', 'website', 'description')) {

    $error = array();

    if (!$company_name) {
        $error[] = "Company name is required.";
    }

    if (!$contact_person) {
        $error[] = "Contact person is required.";
    }

    if (!$contact_email) {
        $error[] = "Contact email is required.";
    }

    if ($password != $password_retype) {
        $error[] = "Passwords do not match.";
    }

    if ($db->customer_email_exists($contact_email)) {
        $error[] = "The specified email is already in use.";
    }

    if (count($error) == 0) {

        $user_id = $db->create_recruiter(
                $password, $contact_email, $contact_person,
                $company_name, $phone, $fax, $website, $description);

        if ($user_id) {
            login_recruiter($user_id);
        } else {
            $error[] = "Failed to add user.";
        }

    }

} else {

    $website = 'http://';

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="signup.css" type="text/css"/>
  </head>
  <body><form action="recruiter_signup.php" method="POST">
    <? include('error.php'); ?>
    <h1>
      Create a Recruiter Account
    </h1>
    <table class="box">
      <tr>
        <td>
          * Company Name
        </td>
        <td>
          <input type="text" value="<? echo $company_name; ?>" name="company_name" />
        </td>
      </tr>
      <tr>
        <td>
          * Contact Person
        </td>
        <td>
          <input type="text" value="<? echo $contact_person; ?>" name="contact_person" />
        </td>
      </tr>
      <tr>
        <td>
          * Contact Email
        </td>
        <td>
          <input type="text" value="<? echo $contact_email; ?>" name="contact_email" />
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
          Tell us about your company
        </td>
      </tr>
      <tr>
        <td>
          Phone
        </td>
        <td>
          <input type="text" value="<? echo $phone; ?>" name="phone" maxlength="10" />
        </td>
      </tr>
      <tr>
        <td>
          Fax
        </td>
        <td>
          <input type="text" value="<? echo $fax; ?>" name="fax" maxlength="10" />
        </td>
      </tr>
      <tr>
        <td>
          Website
        </td>
        <td>
          <input type="text" value="<? echo $website; ?>" name="website" />
        </td>
      </tr>
      <tr>
        <td>
          Short description<br />(Not to exceed<br />500 characters)
        </td>
        <td>
          <textarea name="description"><? echo $description; ?></textarea>
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

