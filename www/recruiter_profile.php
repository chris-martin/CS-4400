<?

require_once('lib.php');

access_recruiter();

$user_id = $_SESSION['user_id'];

if (register_post_keys('phone', 'fax', 'website', 'description')) {

    $db->edit_recruiter($user_id, $phone, $fax, $website, $description);

    goto_continue('Profile updated successfully.',
                  'recruiter_profile.php');

} else {

    $recruiter = $db->get_company($user_id);

    extract($recruiter);

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body><form action="recruiter_profile.php" method="POST">
    <? $tab = 'profile'; include('recruiter_header.php'); ?>
    <h1>Update Recruiter Profile</h1>
    <table class="box">
    <table class="box">
      <tr>
        <td>
          Phone
        </td>
        <td>
          <input type="text" name="phone" value="<? echo $phone; ?>" maxlength="10" />
        </td>
      </tr>
      <tr>
        <td>
          Fax
        </td>
        <td>
          <input type="text" name="fax" value="<? echo $fax; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          Website
        </td>
        <td>
          <input type="text" name="website" value="<? echo $website; ?>" />
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

