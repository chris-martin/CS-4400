<?

require_once('lib.php');

access_applicant();

$user_id = $_SESSION['user_id'];

if (register_post_keys('phone', 'degree', 'birth', 'experience',
                       'citizenship', 'description')) {

    $db->edit_applicant($user_id, $phone, $degree, $experience,
                        $citizenship, $birth, $description);

    goto_continue('Profile updated successfully.',
                  'applicant_profile.php');

} else {

    $applicant = $db->get_applicant($user_id);

    extract($applicant);

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body><form action="applicant_profile.php" method="POST">
    <? $tab = 'profile'; include('applicant_header.php'); ?>
    <h1>Update Applicant Profile</h1>
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
          Highest degree
        </td>
        <td>
          <select name="degree">
          <? foreach ($db->lookup_degree() as $id => $name) { ?>
            <option value="<? echo "$id"; ?>"<?
              if ($id == $degree) { echo ' selected="selected"'; }
            ?>>
              <? echo $name; ?>
            </option>
          <? } ?>
            <option value=""<? if (!$degree) { echo ' selected="selected"'; }?>></option>
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
            <option value="<? echo $i ?>"<?
              if ($id == $birth) { echo ' selected="selected"'; }
            ?>>
              <? echo $i ?>
            </option>
          <? } ?>
            <option value=""<? if (!$birth) { echo ' selected="selected"'; }?>></option>
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
            <option value="<? echo $i ?>"<?
              if ($i == $experience) { echo ' selected="selected"'; }
            ?>>
              <? echo $i ?>
            </option>
          <? } ?>
            <option value=""<? if (!$experience) { echo ' selected="selected"'; }?>></option>
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
            <option value="<? echo "$id"; ?>"<?
              if ($i == $citizenship) { echo ' selected="selected"'; }
            ?>>
              <? echo $name; ?>
            </option>
          <? } ?>
            <option value=""<? if (!$citizenship) { echo ' selected="selected"'; }?>></option>
          </select>
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

