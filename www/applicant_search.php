<?

require_once('lib.php');

access_recruiter();

$lookup_degree = $db->lookup_degree();

$lookup_citizenship = $db->lookup_citizenship();

if ($_GET['searching']) {

    register_optional_get_keys(
        'degree',
        'birth_begin',
        'birth_end',
        'experience',
        'citizenship');

    $results = $db->search_applicants(
        $degree,
        $birth_begin,
        $birth_end,
        $experience,
        $citizenship);

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
  <form action="applicant_search.php" method="GET">
    <input type="hidden" name="searching" value="true"/>
    <? $tab = 'search'; include('recruiter_header.php'); ?>
    <h1>Search for Applicants</h1>
    <? include('error.php'); ?>
    <table class="box">
      <tr>
        <td>
          Minimum degree
        </td>
        <td>
          <select name="degree">
          <? foreach ($lookup_degree as $id => $name) { ?>
            <option value="<? echo "$id"; ?>"<?
              if ($degree == $id) { echo 'selected="selected"'; }
            ?>>
              <? echo $name; ?>
            </option>
          <? } ?>
            <option value=""<? if (!$degree) { echo 'selected="selected"'; } ?>></option>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Born between
        </td>
        <td>
          <input type="text" style="width: 40px;"
                 name="birth_begin" value="<? echo $birth_begin ?>"/>
          and
          <input type="text" style="width: 40px;"
                 name="birth_end" value="<? echo $birth_end ?>"/>
        </td>
      </tr>
            <tr>
        <td>
          Years of experience at least
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
          <? foreach ($lookup_citizenship as $id => $name) { ?>
            <option value="<? echo "$id"; ?>"<?
              if ($id == $citizenship) { echo ' selected="selected"'; }
            ?>>
              <? echo $name; ?>
            </option>
          <? } ?>
            <option value=""<? if (!$citizenship) { echo ' selected="selected"'; }?>></option>
          </select>
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
        <th>Name</th>
        <th>Highest<br/>degree</th>
        <th>Birth<br/>year</th>
        <th>Years&nbsp;of<br/>experience</th>
        <th>Citizenship</th>
      </tr>
    <? foreach ($results as $applicant) { ?>
      <tr>
        <td>
          <a href="view_applicant.php?applicant_id=<? echo $applicant['id']; ?>">
            <? echo $applicant['name']; ?>
          </a>
        </td>
        <td>
          <? echo $lookup_degree[$applicant['degree']]; ?>
        </td>
        <td>
          <? echo $applicant['birth']; ?>
        </td>
        <td>
          <? echo $applicant['experience']; ?>
        </td>
        <td>
          <? echo $lookup_citizenship[$applicant['citizenship']]; ?>
        </td>
      </tr>
    <? } ?>
    </table>
<? } ?>
  </body>
</html>

