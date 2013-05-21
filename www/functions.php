<?

/**
 * Return a new array whose elements (recursively applied) have been stripped
 * of any backslashes that have been added by magic-quotes.
 */
function stripslashes_deep($value) {
    $value = is_array($value)
              ? array_map('stripslashes_deep', $value)
              : stripslashes($value);

    return $value;
}

/**
 * Undoes the damage done by PHP's magic-quotes "feature" if it is enabled.
 */
function kill_magic_quotes() {

    $magic_quotes =
    (
        (
              function_exists("get_magic_quotes_gpc")
           && get_magic_quotes_gpc()
        ) || (
              ini_get('magic_quotes_sybase')
           && strtolower(ini_get('magic_quotes_sybase')) != "off"
        )
    );

    if ($magic_quotes) {
        $_GET = stripslashes_deep($_GET);
        $_POST = stripslashes_deep($_POST);
        $_COOKIE = stripslashes_deep($_COOKIE);
    }

}

/**
 * Returns an array of strings representing every month starting
 * at $month_str and ending with the current month.
 */
function months_from($month_str) {

    $months = array();

    $d = explode('-', $month_str);
    $year = intval($d[0]);
    $month = intval($d[1]);

    while ($year != date('Y') || $month != date('n')) {
        if ($month == 13) {
            $month = 1;
            $year++;
        }
        $m = $year . '-' . str_pad($month, 2, "0", STR_PAD_LEFT);
        $months[] = $m;
        $month++;
    }
    $months[] = current_month();

    return $months;

}

function current_month() {
    return date('Y-m');
}

function format_date($date) {
    return date("M j, Y", $date);
}

function get_login_type() {
    foreach (array('applicant', 'recruiter', 'admin') as $type) {
        if ($_SESSION[$type]) {
            return $type;
        }
    }
    return false;
}

/**
 * Quits if the currently logged-in user is not of the given user type.
 */
function access($type) {

    session_start();

    $current_type = get_login_type();
    if ($current_type != $type) {
        echo "This page is for " . $type . "s only.<br/><br/>";
        if ($current_type) {
            echo "You are: " . $current_type;
        } else {
            echo "You are not logged in.";
        }
        exit();
    }

}

/**
 * Ensure that the currently logged-in user is an applicant.
 */
function access_applicant() {
    access('applicant');
}

/**
 * Ensure that the currently logged-in user is an applicant.
 */
function access_recruiter() {
    access('recruiter');
}

/**
 * Ensure that the currently logged-in user is an administrator.
 */
function access_admin() {
    access('admin');
}

/**
 * Displays a page containing a message and a link to another page.
 *
 * @param message the message to display
 * @param url the url of the page to link to
 */
function goto_continue($message, $url) {
    $GLOBALS['message'] = $message;
    $GLOBALS['url'] = $url;
    require('continue.php');
    session_write_close();
    exit();
}

/**
 * Removes all session variables.
 */
function logout() {
    foreach (array('applicant', 'recruiter', 'admin', 'user_id') as $var) {
        $_SESSION[$var] = null;
    }
}

/**
 * Puts applicant login information into the session.
 */
function login_applicant($user_id) {
    logout();
    $_SESSION['applicant'] = true;
    $_SESSION['user_id'] = $user_id;
    goto_continue('Login successful.', 'job_search.php');
}

/**
 * Puts recruiter login information into the session.
 */
function login_recruiter($user_id) {
    logout();
    $_SESSION['recruiter'] = true;
    $_SESSION['user_id'] = $user_id;
    goto_continue('Login successful.', 'recruiter_status.php');
}

/**
 * Puts admin login information into the session.
 */
function login_admin($user_id) {
    logout();
    $_SESSION['admin'] = true;
    $_SESSION['user_id'] = $user_id;
    goto_continue('Login successful.', 'industry_report.php');
}

/**
 * Issues an http redirect.
 */
function redirect($url) {
    session_write_close();
    header("Location: " . $url);
    exit();
}

function not_null_keys($array, $keys) {
    foreach ($keys as $k) {
        if (!isset($array[$k])) {
            return false;
        }
    }
    return true;
}

function register_keys($array, $keys) {
    if (!not_null_keys($array, $keys)) {
        return false;
    }
    foreach ($keys as $k) {
        $GLOBALS[$k] = $array[$k];
    }
    return true;
}

/**
 * Checks whether all of the function arguments are keys in $_POST.
 * If yes, they are set as variables in the global scope, and return true.
 * Otherwise, return false.
 */
function register_post_keys() {
    $args = func_get_args();
    return register_keys($_POST, $args);
}

/**
 * Checks whether all of the function arguments are keys in $_GET.
 * If yes, they are set as variables in the global scope, and return true.
 * Otherwise, return false.
 */
function register_get_keys() {
    $args = func_get_args();
    return register_keys($_GET, $args);
}

function register_optional_keys($array, $keys) {
    foreach ($keys as $k) {
        if (array_key_exists($k, $array)) {
            $GLOBALS[$k] = $array[$k];
        } else {
            $GLOBALS[$k] = null;
        }
    }
}

/**
 * Checks whether each of the function arguments are keys in $_POST.
 * For each key that is present, set is as a variable in the global scope.?
 */
function register_optional_post_keys() {
    $args = func_get_args();
    return register_optional_keys($_POST, $args);
}

/**
 * Checks whether each of the function arguments are keys in $_GET.
 * For each key that is present, set is as a variable in the global scope.?
 */
function register_optional_get_keys() {
    $args = func_get_args();
    return register_optional_keys($_GET, $args);
}

function value_or_null($str) {
    if ($str === '' || $str === null) {
        return "NULL";
    }
    return value_not_null($str);
}

function value_not_null($str) {
    return "'".mysql_real_escape_string($str)."'";
}

?>
