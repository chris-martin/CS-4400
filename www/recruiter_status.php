<?

require_once('lib.php');

access_recruiter();

$jobs = $db->recruiter_status($_SESSION['user_id']);

function checked_jobs() {
    $jobs = array();
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'job_') === 0) {
            $jobs[] = str_replace('job_', '', $key);
        }
    }
    return $jobs;
}

if ($_POST['close_selected']) {

    foreach (checked_jobs() as $job_id) {
        $db->close_job($job_id);
    }

    goto_continue('The selected jobs have been closed.',
                  'recruiter_status.php');

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body><form action="recruiter_status.php" method="POST">
    <? $tab = 'status'; include('recruiter_header.php'); ?>
    <h1>Jobs Status</h1>
    <table class="box" cellpadding="8">
      <tr>
        <th></th>
        <th>Job<br/>ID</th>
        <th>Job&nbsp;title</th>
        <th>Applicants<br/>waiting&nbsp;for<br/>tests</th>
        <th>Applicants<br/>waiting&nbsp;for<br/>interviews</th>
        <th>Applicants<br/>waiting&nbsp;for<br/>decisions</th>
        <th>Filled<br/>positions</th>
        <th>Requested<br/>positions</th>
        <th>Date<br/>posted</th>
      </tr>
    <? foreach ($jobs as $job) { ?>
      <tr>
        <td><input type="checkbox" class="chk" name="job_<? echo $job['id']; ?>" /></td>
        <td style="text-align: center;">
          <? echo $job['id']; ?>
        </td>
        <td>
          <a href="job_update.php?job_id=<? echo $job['id']; ?>">
            <? echo $job['title']; ?></td>
          </a>
        </td>
        <td style="text-align: center;">
          <? echo $db->count_waiting_for_test($job['id']); ?>
        </td>
        <td style="text-align: center;">
          <? echo $db->count_waiting_for_interview($job['id']); ?>
        </td>
        <td style="text-align: center;">
          <? echo $db->count_waiting_for_decision($job['id']); ?>
        </td>
        <td style="text-align: center;">
          <? echo $db->count_filled_positions($job['id']); ?>
        </td>
        <td style="text-align: center;">
          <? echo ($job['requested'] != 0 ? $job['requested'] : '-'); ?>
        </td>
        <td style="white-space: nowrap;">
          <? echo format_date($job['date']); ?>
        </td>
      </tr>
    <? } ?>
      <tr>
        <td colspan="9">
          <input type="hidden" name="close_selected" value="true" />
          <input type="submit" value="Close selected jobs" class="btn" />
        </td>
      </tr>
    </table>
  </form>
    <!--<h1>Closed Jobs</h1>
    <table class="box" cellpadding="8">
      <tr>
        <th></th>
        <th>Job<br/>ID</th>
        <th>Job&nbsp;title</th>
        <th>Filled<br/>positions</th>
        <th>Requested<br/>positions</th>
        <th>Date<br/>posted</th>
      </tr>
    </table>-->
  </body>
</html>

