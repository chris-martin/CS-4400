<? require_once('lib.php'); ?>
<table class="header" cellspacing="0">
  <tr>
    <td class="tab<? if ($tab == 'industry') { echo 'Selected'; } ?>">
      <a href="industry_report.php">Industry</a>
    </td>
    <td class="tabSpace">
    </td>
    <td class="tab<? if ($tab == 'salary') { echo 'Selected'; } ?>">
      <a href="salary_report.php">Salary</a>
    </td>
    <td class="signOut">
      <a href="sign_out.php">Sign out</a>
    </td>
  </tr>
</table>

