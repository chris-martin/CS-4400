<?

require_once('lib.php');

access_recruiter();

$job_id = $_GET['job_id'];

$job = $db->get_job($job_id);

$lookup_status = $db->lookup_application_status();

$applications = $db->applications_for_job($job_id);

$open_positions = $job['positions'];
foreach ($applications as $app) {
    if ($app['status'] == 5) {
        $open_positions--;
    }
}

function checked_applications() {
    $apps = array();
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'application_') === 0) {
            $apps[] = str_replace('application_', '', $key);
        }
    }
    return $apps;
}

function scores() {
    $scores = array();
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'score_') === 0) {
            $scores[str_replace('score_', '', $key)] = $value;
        }
    }
    return $scores;
}

if ($_POST['selected_action'] == 'advance') {

    $new_status = array();

    $accepted_count = 0;

    foreach (checked_applications() as $application_id) {

        foreach ($applications as $search_app) {
            if ($search_app['id'] == $application_id) {
                $application = $search_app;
            }
        }
        $status = $application['status'];

        if (++$status == 4) { $status++; $accepted_count++; }
        $new_status[$application_id] = $status;

    }

    if ($accepted_count > $open_positions) {
        $error = "Not enough open positions to accept $accepted_count applicants.";
    }

    if (!$error) {

        foreach ($new_status as $application_id => $status) {
            $db->change_application_status($application_id, $status);
        }

        if ($accepted_count === $open_positions) {
            $db->close_job($job_id);
        }

        goto_continue('The selected applicants have been advanced.',
                      'job_update.php?job_id='.$job_id);

    }

} else if ($_POST['selected_action'] == 'decline') {

    foreach (checked_applications() as $application_id) {
        $status = $applications[$application_id]['status'];
        $db->change_application_status($application_id, 4);
    }

    goto_continue('The selected applicants have been declined.',
                  'job_update.php?job_id='.$job_id);

} else if ($_POST['selected_action'] == 'score') {

    foreach (scores() as $application_id => $score) {
        $db->update_test_score($application_id, $score);
    }

    goto_continue('Test scores have been updated.',
                  'job_update.php?job_id='.$job_id);

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body><form action="job_update.php?job_id=<? echo $job_id; ?>" method="POST">
    <input type="hidden" name="selected_action" />
    <? $tab = 'status'; include('recruiter_header.php'); ?>
    <? include('error.php'); ?>
    <h1>
      Job <? echo $job_id; ?>
      &nbsp; &sdot; &nbsp;
      <? echo $job['title']; ?>
      &nbsp; &sdot; &nbsp;
      <? echo $open_positions; ?> unfilled positions
    </h1>
    <table class="box">
      <tr>
        <th></th>
        <th>Applicant<br/>ID</th>
        <th>Applicant<br/>name</th>
        <th>Application<br/>status</th>
        <th>Test<br/>score</th>
      </tr>
    <? foreach ($applications as $app) { ?>
      <tr>
        <td>
          <? if ($app['status'] < 4) { ?>
            <input type="checkbox" class="chk"
                   name="application_<? echo $app['id']; ?>"/>
          <? } ?>
        </td>
        <td>
          <? echo $app['applicant_id']; ?>
        </td>
        <td>
          <a href="view_applicant.php?applicant_id=<? echo $app['applicant_id']; ?>">
            <? echo $app['applicant_name']; ?>
          </a>
        </td>
        <td>
          <? echo $lookup_status[$app['status']]; ?>
        </td>
        <td>
          <input type="text" style="width: 40px;"
                 name="score_<? echo $app['id']; ?>"
                 value="<? echo $app['score']; ?>"/>
        </td>
      </tr>
    <? } ?>
      <tr>
        <td colspan="3">
          <input type="submit" value="Advance" style="width: auto;"
                 onclick="this.form.selected_action.value='advance';" />
          <input type="submit" value="Decline" style="width: auto;"
                 onclick="this.form.selected_action.value='decline';" />
        </td>
        <td colspan="2" style="text-align: right;">
          <input type="submit" value="Save Scores" style="width: auto;"
                 onclick="this.form.selected_action.value='score';" />
        </td>
      </tr>
    </table>
  </form></body>
</html>

