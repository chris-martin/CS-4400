<?

require_once('lib.php');

access_recruiter();

register_get_keys('applicant_id');
$applicant = $db->get_applicant($applicant_id);

$lookup_degree = $db->lookup_degree();
$lookup_citizenship = $db->lookup_citizenship();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body>
    <? include('recruiter_header.php'); ?>
    <h1>Applicant <? echo $applicant_id; ?></h1>
    <table class="box infobox">
      <tr>
        <th>Name</th>
        <td><? echo $applicant['name']; ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><? echo $applicant['email'] ?></td>
      </tr>
      <tr>
        <th>Phone</th>
        <td><? echo $applicant['phone']
                    ? $applicant['phone']
                    : '-'; ?></td>
      </tr>
      <tr>
        <th>Highest degree</th>
        <td><? echo $applicant['degree']
                    ? $lookup_degree[$applicant['degree']]
                    : '-'; ?></td>
      </tr>
      <tr>
        <th>Age</th>
        <td><? echo $applicant['birth']
                    ? date('Y') - $applicant['birth']
                    : '-'; ?></td>
      </tr>
      <tr>
        <th>Years of experience</th>
        <td><? echo $applicant['experience']
                    ? $applicant['experience']
                    : '-'; ?></td>
      </tr>
      <tr>
        <th>Citizenship</th>
        <td><? echo $applicant['citizenship']
                    ? $lookup_citizenship[$applicant['citizenship']]
                    : '-'; ?></td>
      </tr>
      <tr>
        <th>Description</th>
        <td style="width: 15em;">
          <? echo $applicant['description']
                  ? $applicant['description']
                  : '-'; ?>
        </td>
      </tr>
    </table>
  </body>
</html>

