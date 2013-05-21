<?

require_once('lib.php');

access_admin();

$salary_ranges = array(
    array('min' => 0, 'max' => 50000, 'text' => '0 - 50,000'),
    array('min' => 50001, 'max' => 100000, 'text' => '50,001 - 100,000'),
    array('min' => 100001, 'max' => 500000, 'text' => '50,001 - 500,000'),
    array('min' => 500000, 'max' => -1, 'text' => 'Over 500,000')
);

if ($_GET['month']) {
    $start_month = $_GET['month'];
} else {
    $start_month = current_month();
}

$months = months_from($start_month);

$earliest_month = date('Y-m', $db->earliest_job_date());
$month_options = months_from($earliest_month);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <link rel="stylesheet" href="style.css" type="text/css"/>
  </head>
  <body>
    <? $tab = 'salary'; include('admin_header.php'); ?>
    <h1>Salary Report</h1>
    <form action="salary_report.php" method="GET">
      <input type="submit" value="Show report since:"/>
      <select name="month">
      <? foreach ($month_options as $month) { ?>
        <option value="<? echo $month; ?>"><? echo $month; ?></option>
      <? } ?>
      </select>
    </form>
    <table class="box">
  <? foreach ($months as $month) { $a = $b = $c = 0; ?>
      <tr>
        <th style="text-align: left;"><? echo date("F Y", strtotime($month)); ?></th>
        <th style="text-align: center;">New<br/>applications</th>
        <th style="text-align: center;">Available<br/>positions</th>
        <th style="text-align: center;">Unfilled<br/>positions</th>
      </tr>
    <? foreach ($salary_ranges as $range) {
        $new_apps = $db->salary_new_applications($range['min'], $range['max'], $month);
        $total_pos = $db->salary_total_positions($range['min'], $range['max'], $month);
        $fill_pos = $db->salary_filled_positions($range['min'], $range['max'], $month);
    ?>
      <tr>
        <td style="text-align: left;">
            <? echo $range['text']; ?></td>
        <td style="text-align: center;">
            <? $a += $new_apps; echo $new_apps; ?></td>
        <td style="text-align: center;">
            <? $b += $total_pos; echo $total_pos + 0; ?></td>
        <td style="text-align: center;">
            <? $c += $total_pos - $fill_pos; echo $total_pos - $fill_pos; ?></td>
      </tr>
    <? } ?>
      <tr>
        <td>Total</td>
        <td style="text-align: center;"><? echo $a; ?></td>
        <td style="text-align: center;"><? echo $b; ?></td>
        <td style="text-align: center;"><? echo $c; ?></td>
      </tr>
  <? } ?>
    </table>
  </body>
</html>

