<? require_once('lib.php'); ?>
<table class="header" cellspacing="0">
  <tr>
    <td class="hello">
      Hello, <? echo $db->get_customer_name($_SESSION['user_id']); ?>.
    </td>
    <td class="tab<? if ($tab == 'job') { echo 'Selected'; } ?>">
      <a href="job_search.php">Job Search</a>
    </td>
    <td class="tabSpace">
    </td>
    <td class="tab<? if ($tab == 'status') { echo 'Selected'; } ?>">
      <a href="applicant_status.php">Status</a>
    </td>
    <td class="tabSpace">
    </td>
    <td class="tab<? if ($tab == 'profile') { echo 'Selected'; } ?>">
      <a href="applicant_profile.php">Profile</a>
    </td>
    <td class="signOut">
      <a href="sign_out.php">Sign out</a>
    </td>
  </tr>
</table>

