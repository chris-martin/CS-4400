<?

require_once('lib.php');

access_recruiter();

if (register_post_keys('title', 'positions', 'industry',
                       'minimum_salary', 'test', 'minimum_score',
                       'email', 'phone', 'fax', 'description')) {

    $position_types = array();
    foreach ($db->lookup_position_type() as $id => $name) {
        if ($_POST['position_type_' . $id]) {
            $position_types[] = $id;
        }
    }

    $error = array();

    if (!$title) {
        $error[] = "Job title is required.";
    }

    if (count($position_types) == 0) {
        $error[] = "At least one position type is required.";
    }

    if (!$minimum_salary) {
        $error[] = "Minimum salary is required.";
    } else if (!is_numeric($minimum_salary)) {
        $error[] = "Minimum salary must be a number.";
    }

    if ($minimum_score && !is_numeric($minimum_score)) {
        $error[] = "Minimum score must be a number.";
    }

    if (!$email) {
        $error[] = "Contact email is required.";
    }

    if (!$phone) {
        $error[] = "Phone is required.";
    }

    if (strlen($description) > 500) {
        $error[] = "Description cannot exceed 500 characters.";
    }

    if (count($error) == 0) {

        $job_id = $db->post_job(
                    $_SESSION['user_id'], $title, $description,
                    $industry, $minimum_salary, $test, $minimum_score,
                    $email, $phone, $fax, $positions, $position_types);

        goto_continue("Job $job_id posted", 'recruiter_status.php');

    }

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body><form action="post_job.php" method="POST">
    <? $tab = 'post'; include('recruiter_header.php'); ?>
    <? include('error.php'); ?>
    <h1>Post a New Job</h1>
    <table class="box">
      <tr>
        <td>
          * Job title
        </td>
        <td>
          <input type="text" name="title" value="<? echo $title; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          * Number of positions
        </td>
        <td>
          <select name="positions">
          <? for ($i = 100; $i >= 1; $i--) { ?>
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
          Industry
        </td>
        <td>
          <select name="industry">
          <? foreach ($db->lookup_industry() as $id => $name) { ?>
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
          * Position type
        </td>
        <td>
        <? foreach ($db->lookup_position_type() as $id => $name) { ?>
          <input type="checkbox" class="chk"
                 name="position_type_<? echo $id; ?>"/>
          <? echo $name; ?><br/>
        <? } ?>
        </td>
      </tr>
      <tr>
        <td>
          * Minimum salary
        </td>
        <td>
          <input type="text" name="minimum_salary" value="<? echo $minimum_salary; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          Test
        </td>
        <td>
          <select name="test">
          <? foreach ($db->lookup_test_type() as $id => $name) { ?>
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
          Minimum test score
        </td>
        <td>
          <input type="text" name="minimum_score" value="<? echo $minimum_score; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          * Contact email
        </td>
        <td>
          <input type="text" name="email" value="<? echo $email; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          * Phone
        </td>
        <td>
          <input type="text" name="phone" value="<? echo $phone; ?>" />
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
          Job description<br />(Not to exceed<br />500 characters)
        </td>
        <td>
          <textarea name="description"><? echo $description; ?></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="submitCell">
          <input type="submit" value="Post job" class="btn" />
        </td>
      </tr>
    </table>
  </form></body>
</html>

