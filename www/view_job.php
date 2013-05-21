<?

require_once('lib.php');

access_applicant();

$user_id = $_SESSION['user_id'];

if (register_post_keys('apply', 'job_id')) {

    if ($db->get_application_id($user_id, $job_id)) {
        goto_continue('You have already applied for this job.',
                      'applicant_status.php');
    }

    $db->apply($user_id, $job_id);
    goto_continue('Your application has been submitted.',
                  'applicant_status.php');

}

register_get_keys('job_id');
$job = $db->get_job($job_id);

$lookup_position_type = $db->lookup_position_type();
$lookup_industry = $db->lookup_industry();
$lookup_test_type = $db->lookup_test_type();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body>
    <? include('applicant_header.php'); ?>
    <h1>Job: <? echo $job['title']; ?></h1>
    <table class="box infobox">
      <tr>
        <th>Number of positions</th>
        <td><? echo $job['positions']; ?></td>
      </tr>
      <tr>
        <th>Industry</th>
        <td><? echo $job['industry'] ? $lookup_industry[$job['industry']] : '-'; ?></td>
      </tr>
      <tr>
        <th>Position type</th>
        <td style="white-space: nowrap;">
          <? foreach ($job['position_type'] as $position_type) { ?>
            <? echo $lookup_position_type[$position_type]; ?><br/>
          <? } ?>
        </td>
      </tr>
      <tr>
        <th>Minimum salary</th>
        <td><? echo $job['salary']; ?></td>
      </tr>
      <tr>
        <th>Test</th>
        <td><? echo $job['test'] ? $lookup_test_type[$job['test']] : 'No test'; ?></td>
      </tr>
      <tr>
        <th>Minimum test score</th>
        <td><? echo $job['test_score']; ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><? echo $job['email']; ?></td>
      </tr>
      <tr>
        <th>Fax</th>
        <td><? echo $job['fax']; ?></td>
      </tr>
      <tr>
        <th>Description</th>
        <td style="width: 15em;"><? echo $job['description']; ?></td>
      </tr>
    <? if (!$db->get_application_id($user_id, $job_id)) { ?>
      <tr>
        <td colspan="2" class="submitCell">
          <form action="view_job.php" method="POST">
            <input type="hidden" name="apply" value="true" />
            <input type="hidden" name="job_id" value="<? echo $job_id; ?>" />
            <input type="submit" value="Apply" class="btn" />
          </form>
        </td>
      </tr>
    <? } ?>
    </table>
  </body>
</html>

