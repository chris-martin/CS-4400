<?

require_once('lib.php');

access_applicant();

$lookup_application_status = $db->lookup_application_status();

register_get_keys('show_all');
$show_all = $show_all && ($show_all === 'true');

$jobs = $db->get_applications_for_applicant($_SESSION['user_id'], $show_all);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body>
    <? $tab = 'status'; include('applicant_header.php'); ?>
    <h1>Applications Status</h1>
    <table class="box" cellpadding="8">
      <tr>
        <th>Job&nbsp;title</th>
        <th>Employer</th>
        <th>Date&nbsp;applied</th>
        <th>Status</th>
      </tr>
    <? foreach ($jobs as $job) { ?>
      <tr>
        <td>
          <a href="view_job.php?job_id=<? echo $job['id']; ?>">
            <? echo $job['title']; ?></td>
          </a>
        </td>
        <td>
          <a href="view_employer.php?recruiter_id=<? echo $job['recruiter_id']; ?>">
            <? echo $job['company']; ?>
          </a>
        </td>
        <td>
          <? echo format_date($job['date_applied']); ?>
        </td>
        <td>
          <? echo $lookup_application_status[$job['status']]; ?>
        </td>
      </tr>
    <? } ?>
    <tr>
      <td colspan="4">
        <form action="applicant_status.php" method="GET">
          <input type="hidden" name="show_all"
                 value="<? echo $show_all ? 'false' : 'true'; ?>"/>
          <input type="submit"
                 value="<? echo $show_all
                                  ? 'Show only jobs in process'
                                  : 'Show all jobs';
                        ?>" />
        </form>
      </td>
    </tr>
    </table>
  </body>
</html>

