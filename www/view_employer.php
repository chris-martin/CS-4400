<?

require_once('lib.php');

access_applicant();

$employer = $db->get_company($_GET['recruiter_id']);

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
    <h1>Employer: <? echo $employer['name']; ?></h1>
    <table class="box infobox">
      <tr>
        <th>Contact person</th>
        <td><? echo $employer['person']; ?></td>
      </tr>
      <tr>
        <th>Contact email</th>
        <td><? echo $employer['email']; ?></td>
      </tr>
      <tr>
        <th>Phone</th>
        <td><? echo $employer['phone']; ?></td>
      </tr>
      <tr>
        <th>Fax</th>
        <td><? echo $employer['fax']; ?></td>
      </tr>
      <tr>
        <th>Website</th>
        <td><? echo $employer['website']; ?></td>
      </tr>
      <tr>
        <th>Description</th>
        <td style="width: 15em;"><? echo $employer['description']; ?></td>
      </tr>
    </table>
  </body>
</html>

