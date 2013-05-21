<?

require_once('lib.php');

access_applicant();

$lookup_position_type = $db->lookup_position_type();

$lookup_industry = $db->lookup_industry();

if ($_GET['searching']) {

    $checked_position_types = array();
    foreach ($db->lookup_position_type() as $id => $name) {
        if ($_GET['position_type_' . $id]) {
            $checked_position_types[] = $id;
        }
    }

    register_optional_get_keys(
        'industry',
        'title_keywords',
        'minimum_salary');

    $results = $db->search_jobs($industry,
                                explode(' ', $title_keywords),
                                $checked_position_types,
                                $minimum_salary);

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body>
  <form action="job_search.php" method="GET">
    <input type="hidden" name="searching" value="true"/>
    <? $tab = 'job'; include('applicant_header.php'); ?>
    <h1>Job Search</h1>
    <? include('error.php'); ?>
    <table class="box">
      <tr>
        <td>
          Position type
        </td>
        <td>
        <? foreach ($lookup_position_type as $id => $name) { ?>
          <input type="checkbox" name="position_type_<? echo $id; ?>"<?
            if (count($checked_position_types) != 0) {
              if (in_array($id, $checked_position_types)) {
                echo ' checked="true"';
              }
            }
          ?> class="chk"/>
          <? echo $name; ?><br/>
        <? } ?>
        </td>
      </tr>
      <tr>
        <td>
          Industry
        </td>
        <td>
          <select name="industry">
          <? foreach ($lookup_industry as $id => $name) { ?>
            <option value="<? echo "$id"; ?>"<?
              if ($industry == $id) { echo 'selected="selected"'; }
            ?>>
              <? echo $name; ?>
            </option>
          <? } ?>
            <option value=""<? if (!$industry) { echo 'selected="selected"'; } ?>></option>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Keywords in job title
        </td>
        <td>
          <input type="text"
                 name="title_keywords"
                 value="<? echo $title_keywords; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          Minimum salary
        </td>
        <td>
          <input type="text"
                 name="minimum_salary"
                 value="<? echo $minimum_salary; ?>" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="submitCell">
          <input type="submit" value="Search" class="btn" />
        </td>
      </tr>
    </table>
  </form>
<? if ($results) { ?>
    <h1>Search Results</h1>
    <table class="box">
      <tr>
        <th>Job&nbsp;title</th>
        <th>Employer</th>
        <th>Position<br/>type</th>
        <th>Industry</th>
        <th>Minumum<br/>salary</th>
      </tr>
    <? foreach ($results as $job) { ?>
      <tr>
        <td>
          <a href="view_job.php?job_id=<? echo $job['id']; ?>">
            <? echo $job['title']; ?>
          </a>
        </td>
        <td>
          <a href="view_employer.php?recruiter_id=<? echo $job['recruiter_id']; ?>">
            <? echo $job['employer']; ?>
          </a>
        </td>
        <td style="white-space: nowrap;">
          <? foreach ($job['position_type'] as $position_type) { ?>
            <? echo $lookup_position_type[$position_type]; ?><br/>
          <? } ?>
        </td>
        <td>
          <? echo $lookup_industry[$job['industry']]; ?><br/>
        </td>
        <td>
          <? echo $job['minimum_salary']; ?>
        </td>
      </tr>
    <? } ?>
    </table>
<? } ?>
  </body>
</html>

