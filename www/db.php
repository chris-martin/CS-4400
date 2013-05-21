<?php

class Database {

    private $dblink;

    function __construct() {

        $host = "localhost";
        $user = "CareerWorks";
        $pass = "pineapple";
        $db_name = "CareerWorks";

        // Connect to the DB server
        $dblink = @mysql_connect($host, $user, $pass);

        // Select the database
        mysql_select_db($db_name, $dblink);

        // Hold a reference to the DB server resource
        $this->dblink = $dblink;

    }

    private function transaction_start() {

        mysql_query("START TRANSACTION");

    }

    private function transaction_rollback() {

        mysql_query("ROLLBACK");

    }

    private function transaction_commit() {

        mysql_query("COMMIT");

    }

    /*
     * Executes a query, catching any MySQL errors.  Returns 
     * resulting set if valid query.
     */
    private function doQuery($query) {

        // Execute the query
        $result = mysql_query($query, $this->dblink);

        // If the result is false, the query was bad
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error();
            die($message);
        }

        // Return the result
        return $result;

    }

    /* 
     * Gets a lookup table from the database, and returns
     * the table's contents as an array.
     * 
     * Degree is an example of a lookup table.
     */
    private function get_lookup_table($table) {

        // Fetch the entire table
        $result = $this->doQuery(sprintf("
              SELECT  ID,
                      NAME
                FROM  %s LU
            ORDER BY  ID ASC",
            $table
        ));

        // Pull the results into an array (map from ID to NAME)
        $arr = array();
        while ($row = mysql_fetch_assoc($result)) {
            $arr[$row['ID']] = $row['NAME'];
        }

        // Return the array
        return $arr;

    }

    /**
     * Retrieves every application status, indexed by id.
     */
    public function lookup_application_status() {

        return $this->get_lookup_table("APPLICATION_STATUS_LU");

    }

    /**
     * Retrieves every citizenship option, indexed by id.
     */

    public function lookup_citizenship() {

        return $this->get_lookup_table("CITIZENSHIP_LU");

    }

    /**
     * Retrieves every degree option, indexed by id.
     */
    public function lookup_degree() {

        return $this->get_lookup_table("DEGREE_LU");

    }

    /**
     * Retrieves every industry, indexed by id.
     */
    public function lookup_industry() {

        return $this->get_lookup_table("INDUSTRY_LU");

    }

    /**
     * Retrieves every job position type, indexed by id.
     */
    public function lookup_position_type() {

        return $this->get_lookup_table("POSITION_TYPE_LU");

    }

    /**
     * Retrieves every test type, indexed by id.
     */
    public function lookup_test_type() {

        return $this->get_lookup_table("TEST_TYPE_LU");

    }

    /**
     * Returns a boolean indicating whether a customer exists
     * having the given email address.  Used to ensure a user
     * can't sign up and attempt to violate the uniqueness constraint.
     */
    public function customer_email_exists($email) {

        $result = $this->doQuery(sprintf("
            SELECT  USER_ID
              FROM  CUSTOMER
             WHERE  EMAIL = '%s'",
            mysql_real_escape_string($email)
        ));

        $row = mysql_fetch_assoc($result);

        return ($row ? true : false);

    }
    
    /*
     * A utility function to determine whether a customer login is valid.
     * On valid customer login, returns a corresponding user id.
     *
     * 
     */
    private function customer_login($table, $email, $password) {

        // Fetch the id of the user with this email and password
        $result = $this->doQuery(sprintf("
              SELECT  C.USER_ID
                FROM  CUSTOMER C,
                      %s A
               WHERE  C.USER_ID = A.USER_ID
                 AND  C.EMAIL = '%s'
                 AND  C.PASSWORD = '%s';",
            $table,
            mysql_real_escape_string($email),
            mysql_real_escape_string($password)
        ));
        $row = mysql_fetch_assoc($result);

        // If no such user exists, login fails
        if (!$row) {
            return false;
        }

        // Successful login, return the user id
        return $row['USER_ID'];

    }

    /**
     * Validates the login for an applicant.
     *
     * If authenticated succeeds, return the applicant's id.
     * Otherwise, return false.
     */
    public function applicant_login($email, $password) {

        return $this->customer_login('APPLICANT', $email, $password);

    }

    /**
     * Validates the login for a recruiter.
     *
     * If authenticated succeeds, return the recruiter's id.
     * Otherwise, return false.
     */
    public function recruiter_login($email, $password) {

        return $this->customer_login('RECRUITER', $email, $password);

    }

    /**
     * Validates the login for an admin.
     *
     * If authenticated succeeds, return the admin's id.
     * Otherwise, return false.
     */
    public function admin_login($password) {

        $result = $this->doQuery(sprintf("
              SELECT  ADMIN_ID
                FROM  ADMINISTRATOR
               WHERE  PASSWORD = '%s';",
            mysql_real_escape_string($password)
        ));

        $row = mysql_fetch_assoc($result);

        if (!$row) {
            return false;
        }

        $id = $row['ADMIN_ID'];

        return $id;

    }

    /**
     * Gets the name of a customer.
     *
     * @param $user_id the customer's id
     */
    public function get_customer_name($user_id) {

        $result = $this->doQuery(sprintf("
              SELECT  C.NAME
                FROM  CUSTOMER C
               WHERE  C.USER_ID = '%s';",
            mysql_real_escape_string($user_id)
        ));

        $row = mysql_fetch_assoc($result);

        return $row['NAME'];

    }

    /**
     * Gets the date of the earliest-posted job.
     * Used to determine how far back reports can be generated.
     */
    public function earliest_job_date() {

        $result = $this->doQuery("
              SELECT  J.POST_DATE
                FROM  JOB J
            ORDER BY  POST_DATE ASC
               LIMIT  1");

        if ($row = mysql_fetch_assoc($result)) {
            return strtotime($row['POST_DATE']);
        } else {
            return time();
        }

    }

    /**
     * Job search.
     *
     * @param $industry (optional)
     * @param $keywords array of keywords
     * @param $position_types array of position types
     * @param $minimum_salary (optional)
     */
    public function search_jobs($industry, $keywords,
                                $position_types, $minimum_salary) {

        $keyword_string = '%';
        foreach($keywords as $word) {
            $keyword_string .= mysql_real_escape_string($word) . '%';
        }

        $query = sprintf("
              SELECT  J.JOB_ID,
                      J.TITLE,
                      R.USER_ID AS RECRUITER_ID,
                      R.COMPANY_NAME AS EMPLOYER,
                      J.INDUSTRY,
                      J.MINIMUM_SALARY
                FROM  JOB J,
                      RECRUITER R
               WHERE  J.POSTED_BY = R.USER_ID
                 AND  J.TITLE LIKE '%s'
                 AND  J.ACTIVE = '1' ",
            $keyword_string
        );

        if ($minimum_salary) {
            $query .= sprintf("
                     AND  J.MINIMUM_SALARY >= '%s'",
                    mysql_real_escape_string($minimum_salary)
            );

        }

        if ($industry) {
            $query .= sprintf("
                     AND  J.INDUSTRY = '%s'",
                    mysql_real_escape_string($industry)
            );
        }

        if (count($position_types) != 0) {
            $query .= " AND ( 0 = 1 ";
            foreach ($position_types as $type) {
                $query .= sprintf("
                     OR  J.JOB_ID IN (SELECT  T.JOB_ID
                                        FROM  JOB_POSITION_TYPE T
                                       WHERE  T.POSITION_TYPE = '%s')",
                    mysql_real_escape_string($type)
                );
            }
            $query .= ")";
        }

        $query .= "
              ORDER BY  J.TITLE ASC;";

        $result = $this->doQuery($query);

        $search_results = array();

        while ($row = mysql_fetch_assoc($result)) {

            $job_id = $row['JOB_ID'];

            $job_position_types = array();
            $position_type_results = $this->doQuery(sprintf("
                SELECT  POSITION_TYPE
                  FROM  JOB_POSITION_TYPE T
                 WHERE  T.JOB_ID = '%s';",
                mysql_real_escape_string($job_id)
            ));
            while ($position_type_row = mysql_fetch_assoc($position_type_results)) {
                $job_position_types[] = $position_type_row['POSITION_TYPE'];
            }

            $search_results[] = array(
                'id' => $job_id,
                'title' => $row['TITLE'],
                'recruiter_id' => $row['RECRUITER_ID'],
                'employer' => $row['EMPLOYER'],
                'industry' => $row['INDUSTRY'],
                'minimum_salary' => $row['MINIMUM_SALARY'],
                'position_type' => $job_position_types
            );

        }

        return $search_results;

    }

    /**
     * Applicant search.
     *
     * @param $degree (optional)
     * @param $birth_begin (optional)
     * @param $birth_end (optional)
     * @param $experience (optional)
     * @param $citizenship (optional)
     */
    public function search_applicants($degree, $birth_begin, $birth_end,
                                      $experience, $citizenship) {

        $query = "
                SELECT  C.USER_ID,
                        C.NAME,
                        A.HIGHEST_DEGREE,
                        A.BIRTH_YEAR,
                        A.YEARS_EXPERIENCE,
                        A.CITIZENSHIP
                  FROM  CUSTOMER C,
                        APPLICANT A
                 WHERE  C.USER_ID = A.USER_ID";

        if ($degree) {
            $query .= sprintf("
                   AND  A.HIGHEST_DEGREE >= '%s'",
                mysql_real_escape_string($degree)
            );
        }

        if ($birth_begin) {
            $query .= sprintf("
                   AND  A.BIRTH_YEAR >= '%s'",
                mysql_real_escape_string($birth_begin)
            );
        }

        if ($birth_end) {
            $query .= sprintf("
                   AND  A.BIRTH_YEAR <= '%s'",
                mysql_real_escape_string($birth_end)
            );
        }

        if ($experience) {
            $query .= sprintf("
                   AND  A.EXPERIENCE >= '%s'",
                mysql_real_escape_string($experience)
            );
        }

        if ($citizenship) {
            $query .= sprintf("
                   AND  A.CITIZENSHIP = '%s'",
                mysql_real_escape_string($citizenship)
            );
        }

        $query .= "
              ORDER BY  C.NAME ASC;";

        $result = $this->doQuery($query);

        $search_results = array();

        while ($row = mysql_fetch_assoc($result)) {

            $search_results[] = array(
                'id' => $row['USER_ID'],
                'name' => $row['NAME'],
                'degree' => $row['HIGHEST_DEGREE'],
                'birth' => $row['BIRTH_YEAR'],
                'experience' => $row['YEARS_EXPERIENCE'],
                'citizenship' => $row['CITIZENSHIP']
            );

        }

        return $search_results;

    }

    /**
     * Creates a new customer account.
     */
    private function create_customer($password, $email, $name) {

        $this->doQuery(sprintf("
              INSERT  INTO  CUSTOMER (PASSWORD, EMAIL, NAME)
              VALUES  ('%s', '%s', '%s');",
            mysql_real_escape_string($password),
            mysql_real_escape_string($email),
            mysql_real_escape_string($name)
        ));

        return mysql_insert_id();

    }

    /**
     * Creates a new recruiter account.
     */
    public function create_recruiter($password, $email, $name,
            $company_name, $phone, $fax, $website, $description) {

        $this->transaction_start();

        // Insert the customer record
        $id = $this->create_customer($password, $email, $name);

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        // Insert the recruiter record
        $this->doQuery(sprintf("
              INSERT  INTO RECRUITER (USER_ID, COMPANY_NAME, PHONE,
                                      FAX, WEBSITE, DESCRIPTION)
              VALUES  ('%s', '%s', '%s', '%s', '%s', '%s');",
            $id,
            mysql_real_escape_string($company_name),
            mysql_real_escape_string($phone),
            mysql_real_escape_string($fax),
            mysql_real_escape_string($website),
            mysql_real_escape_string($description)
        ));

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        $this->transaction_commit();

        if (mysql_error()) {
            return false;
        }

        // Return the id of the inserted records
        return $id;

    }

    /**
     * Creates a new applicant account.
     */
    public function create_applicant($password, $email, $name,
            $phone, $degree, $experience, $citizenship, $birth, $description) {

        $this->transaction_start();

        // Insert the customer record
        $id = $this->create_customer($password, $email, $name);

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        // Insert the applicant record
        $query = sprintf("
              INSERT  INTO APPLICANT (USER_ID, PHONE, HIGHEST_DEGREE,
                                      YEARS_EXPERIENCE, CITIZENSHIP,
                                      BIRTH_YEAR, DESCRIPTION)
              VALUES  ('%s', %s, %s, %s, %s, %s, %s);",
            $id,
            value_or_null($phone),
            value_or_null($degree),
            value_or_null($experience),
            value_or_null($citizenship),
            value_or_null($birth),
            value_or_null($description)
        );
        $this->doQuery($query);

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        $this->transaction_commit();

        if (mysql_error()) {
            return false;
        }

        // Return the id of the inserted records
        return $id;

    }

    /**
     * Modifies an applicant account.
     */
    public function edit_applicant($user_id, $phone, $degree, $experience,
                                   $citizenship, $birth, $description) {

        $query = sprintf("
              UPDATE  APPLICANT
                 SET  PHONE = '%s',
                      HIGHEST_DEGREE = '%s',
                      YEARS_EXPERIENCE = '%s',
                      CITIZENSHIP = '%s',
                      BIRTH_YEAR = '%s',
                      DESCRIPTION = '%s'
               WHERE  USER_ID = '%s';",
                mysql_real_escape_string($phone),
                mysql_real_escape_string($degree),
                mysql_real_escape_string($experience),
                mysql_real_escape_string($citizenship),
                mysql_real_escape_string($birth),
                mysql_real_escape_string($description),
                mysql_real_escape_string($user_id)
        );

        $this->doQuery($query);

    }

    /**
     * Modifies a recruiter account.
     */
    public function edit_recruiter($user_id, $phone, $fax,
                                   $website, $description) {

        $query = sprintf("
              UPDATE  RECRUITER
                 SET  PHONE = '%s',
                      FAX = '%s',
                      WEBSITE = '%s',
                      DESCRIPTION = '%s'
               WHERE  USER_ID = '%s';",
                mysql_real_escape_string($phone),
                mysql_real_escape_string($fax),
                mysql_real_escape_string($website),
                mysql_real_escape_string($description),
                mysql_real_escape_string($user_id)
        );

        $this->doQuery($query);

    }

    /**
     * Creates a new job.
     *
     * @param $posted_by the id of the recruiter posting the job
     * @param $position_types [array]
     */
    public function post_job($posted_by, $title, $description,
                             $industry, $minimum_salary, $test,
                             $minimum_score, $email, $phone,
                             $fax, $positions, $position_types) {

        $this->transaction_start();

        // Insert the job
        $this->doQuery(sprintf("
            INSERT  INTO JOB (POSTED_BY, POST_DATE,
                    TITLE, DESCRIPTION, INDUSTRY, MINIMUM_SALARY,
                    TEST_TYPE, MIN_TEST_SCORE, EMAIL, PHONE, FAX, 
                    NUM_POSITIONS)
          VALUES (%s, '%s', %s, %s, %s, %s,
                  %s, %s, %s, %s, %s, %s);",
                value_not_null($posted_by),
                date("Y-m-d"),
                value_not_null($title),
                value_or_null($description),
                value_or_null($industry),
                value_not_null($minimum_salary),
                value_or_null($test),
                value_or_null($minimum_score),
                value_not_null($email),
                value_not_null($phone),
                value_or_null($fax),
                value_not_null($positions)
        ));

        $id = mysql_insert_id();

        if ($err = mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        // Insert each position type
        foreach ($position_types as $type) {

            $this->doQuery(sprintf("
                INSERT INTO  JOB_POSITION_TYPE(JOB_ID, POSITION_TYPE)
                     VALUES  ('%s', '%s');",
                $id,
                mysql_real_escape_string($type)
            ));

            if (mysql_error()) {
                $this->transaction_rollback();
                return false;
            }

        }

        $this->transaction_commit();

        if (mysql_error()) {
            return false;
        }

        // Return the id of the inserted job
        return $id;

    }

    /**
     * For each job posted by the given recruiter, fetch some statistics.
     *
     * @param $user_id the id of the recruiter
     */
    public function recruiter_status($user_id) {

        $result = $this->doQuery("

          SELECT  J.JOB_ID,
                  J.TITLE,
                  J.POST_DATE,
                  J.NUM_POSITIONS,
                  J.POST_DATE
            FROM  JOB J" . sprintf("
           WHERE  J.POSTED_BY = '%s'
             AND  J.ACTIVE = '1';",
            mysql_real_escape_string($user_id)
        ));

        $jobs = array();

        while ($row = mysql_fetch_assoc($result)) {
            $jobs[] = array(
                'id' => $row['JOB_ID'],
                'title' => $row['TITLE'],
                'date' => strtotime($row['POST_DATE']),
                'requested' => $row['NUM_POSITIONS']
            );
        }

        return $jobs;

    }

    private function count_applications_with_status($job_id, $status) {

        $result = $this->doQuery(sprintf("
                     SELECT COUNT(*) AS C
                      FROM  APPLICATION A,
                            JOB J
                     WHERE  A.JOB_ID = J.JOB_ID
                       AND  A.STATUS = '%s'
                       AND  J.JOB_ID = '%s'",
                mysql_real_escape_string($status),
                mysql_real_escape_string($job_id)
        ));

        $row = mysql_fetch_assoc($result);

        return $row['C'];

    }

    /**
     * Returns the number of applicants waiting for a test per particular job.
     */
    public function count_waiting_for_test($job_id) {

        return $this->count_applications_with_status($job_id, '1');

    }

    /**
     * Returns the number of applicants waiting for an interview per particular job.
     */
    public function count_waiting_for_interview($job_id) {

        return $this->count_applications_with_status($job_id, '2');

    }

    /**
     * Returns the number of applicants waiting for a decision per particular job.
     */
    public function count_waiting_for_decision($job_id) {

        return $this->count_applications_with_status($job_id, '3');

    }

    /**
     * Returns the number of filled positions.
     */
    public function count_filled_positions($job_id) {

        return $this->count_applications_with_status($job_id, '5');

    }

    /**
     * Closes a job and declines all pending applicants.
     *
     * @param job_id the id of the job to close
     */
    public function close_job($job_id) {

        $this->transaction_start();

        $this->doQuery(sprintf("
            UPDATE  APPLICATION
               SET  STATUS = '4'
             WHERE  JOB_ID = '%s'
               AND  STATUS <> '5'",
            mysql_real_escape_string($job_id)
        ));

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        $this->doQuery(sprintf("
            UPDATE  JOB
               SET  ACTIVE = '0'
             WHERE  JOB_ID = '%s'",
            mysql_real_escape_string($job_id)
        ));

        if (mysql_error()) {
            $this->transaction_rollback();
            return false;
        }

        $this->transaction_commit();

    }

    /**
     * Retrieves all of the data for a particular job.
     *
     * @param $job_id the id of the job to investigate
     */
    public function get_job($job_id) {

        $result = $this->doQuery(sprintf("
              SELECT  J.TITLE,
                      J.NUM_POSITIONS,
                      J.INDUSTRY,
                      J.MINIMUM_SALARY,
                      J.TEST_TYPE,
                      J.MIN_TEST_SCORE,
                      J.EMAIL,
                      J.FAX,
                      J.DESCRIPTION
                FROM  JOB J
               WHERE  J.JOB_ID = '%s';",
            mysql_real_escape_string($job_id)
        ));

        $row = mysql_fetch_assoc($result);

        if (!$row) {
            return false;
        }

        $job = array(
            'title' => $row['TITLE'],
            'positions' => $row['NUM_POSITIONS'],
            'industry' => $row['INDUSTRY'],
            'salary' => $row['MINIMUM_SALARY'],
            'test' => $row['TEST_TYPE'],
            'test_score' => $row['MIN_TEST_SCORE'],
            'email' => $row['EMAIL'],
            'fax' => $row['FAX'],
            'description' => $row['DESCRIPTION']
        );

        $result = $this->doQuery(sprintf("
              SELECT  T.POSITION_TYPE
                FROM  JOB_POSITION_TYPE T
               WHERE  T.JOB_ID = '%s'",
            mysql_real_escape_string($job_id)
        ));

        $position_types = array();
        while ($row = mysql_fetch_assoc($result)) {
            $position_types[] = $row['POSITION_TYPE'];
        }

        $job['position_type'] = $position_types;

        return $job;

    }

    /**
     * Sets the test score for an application.
     *
     * @param $application_id the id of the application
     * @param $score the test score to set for the application
     */
    public function update_test_score($application_id, $score) {

        $this->doQuery(sprintf("
              UPDATE  APPLICATION
                 SET  TEST_SCORE='%s'
               WHERE  APPLICATION_ID='%s';",
            mysql_real_escape_string($score),
            mysql_real_escape_string($application_id)
        ));

    }

    /**
     * Retrieves information about a given applicant.
     */
    public function get_applicant($applicant_id) {

        $result = $this->doQuery(sprintf("
              SELECT  C.NAME,
                      C.EMAIL,
                      A.PHONE,
                      A.HIGHEST_DEGREE,
                      A.YEARS_EXPERIENCE,
                      A.CITIZENSHIP,
                      A.BIRTH_YEAR,
                      A.DESCRIPTION
                FROM  APPLICANT A,
                      CUSTOMER C
               WHERE  A.USER_ID = C.USER_ID
                 AND  A.USER_ID = '%s';",
            mysql_real_escape_string($applicant_id)
        ));

        $row = mysql_fetch_assoc($result);

        if (!$row) {
            return false;
        }

        $applicant = array(
            'name' => $row['NAME'],
            'email' => $row['EMAIL'],
            'phone' => $row['PHONE'],
            'degree' => $row['HIGHEST_DEGREE'],
            'experience' => $row['YEARS_EXPERIENCE'],
            'citizenship' => $row['CITIZENSHIP'],
            'birth' => $row['BIRTH_YEAR'],
            'description' => $row['DESCRIPTION']
        );

        return $applicant;

    }

    /**
     * Returns all applications matching this job id.
     */
    public function applications_for_job($job_id) {

        $result = $this->doQuery(sprintf("
              SELECT  APPLICATION.APPLICATION_ID,
                      APPLICANT.USER_ID AS APPLICANT_ID,
                      APPLICANT.NAME AS APPLICANT_NAME,
                      APPLICATION.STATUS,
                      APPLICATION.TEST_SCORE
                FROM  APPLICATION,
                      CUSTOMER APPLICANT
               WHERE  APPLICATION.APPLICANT_ID = APPLICANT.USER_ID
                 AND  APPLICATION.JOB_ID = '%s';",
            mysql_real_escape_string($job_id)
        ));

        $applications = array();

        while ($row = mysql_fetch_assoc($result)) {
            $applications[] = array(
                'id' => $row['APPLICATION_ID'],
                'applicant_id' => $row['APPLICANT_ID'],
                'applicant_name' => $row['APPLICANT_NAME'],
                'status' => $row['STATUS'],
                'score' => $row['TEST_SCORE']
            );
        }

        return $applications;

    }

    /**
     * Retrieves the company details for a given recruiter.
     */
    public function get_company($recruiter_id) {

        $result = $this->doQuery(sprintf("
              SELECT  R.COMPANY_NAME,
                      C.NAME AS RECRUITER_NAME,
                      C.EMAIL,
                      R.PHONE,
                      R.FAX,
                      R.WEBSITE,
                      R.DESCRIPTION
                FROM  CUSTOMER C,
                      RECRUITER R
               WHERE  C.USER_ID = R.USER_ID
                 AND  R.USER_ID = '%s';",
            mysql_real_escape_string($recruiter_id)
        ));

        $row = mysql_fetch_assoc($result);

        if (!$row) {
            return false;
        }

        $company = array(
            'name' => $row['COMPANY_NAME'],
            'person' => $row['RECRUITER_NAME'],
            'email' => $row['EMAIL'],
            'phone' => $row['PHONE'],
            'fax' => $row['FAX'],
            'website' => $row['WEBSITE'],
            'description' => $row['DESCRIPTION']
        );

        return $company;

    }

    /**
     * Retrieves the application id associated with this applicant and job.
     */
    public function get_application_id($applicant_id, $job_id) {

        $result = $this->doQuery(sprintf("
            SELECT  APPLICATION_ID
              FROM  APPLICATION
             WHERE  APPLICANT_ID = '%s'
               AND  JOB_ID = '%s'",
            mysql_real_escape_string($applicant_id),
            mysql_real_escape_string($job_id)
        ));

        $row = mysql_fetch_assoc($result);

        if (!$row) {
            return false;
        }

        return $row['APPLICATION_ID'];

    }

    /**
     * Creates a new application for a job.
     *
     * @param $applicant_id the id of the applicant who is applying for the job
     * @param $job_id the id of the job the applicant is applying for
     */
    public function apply($applicant_id, $job_id) {

        $this->doQuery(sprintf("
              INSERT  INTO APPLICATION (APPLICANT_ID, JOB_ID, OPEN_DATE)
                      VALUES ('%s', '%s', '%s');",
                mysql_real_escape_string($applicant_id),
                mysql_real_escape_string($job_id),
                date("Y-m-d")
        ));

        $id = mysql_insert_id();

        return $id;

    }

    /**
     * Retrieves details about each application made by a given applicant.
     *
     * @param $applicant_id the id of the applicant
     * @param $show_all true to show all applications,
     *                  false to show only jobs in process
     */
    public function get_applications_for_applicant($applicant_id, $show_all) {

        $query = sprintf("
              SELECT  J.JOB_ID,
                      J.TITLE,
                      R.COMPANY_NAME,
                      R.USER_ID AS RECRUITER_ID,
                      A.OPEN_DATE,
                      A.STATUS
                FROM  JOB J,
                      RECRUITER R,
                      APPLICATION A
               WHERE  J.POSTED_BY = R.USER_ID
                 AND  A.JOB_ID = J.JOB_ID
                 AND  A.APPLICANT_ID = '%s'",
                mysql_real_escape_string($applicant_id)
        );

        if (!$show_all) {
            $query .= "
                 AND  A.CLOSE_DATE IS NULL";
        }

        $query .= ";";

        $result = $this->doQuery($query);

        $applications = array();

        while ($row = mysql_fetch_assoc($result)) {
            $applications[] = array(
                'id' => $row['JOB_ID'],
                'title' => $row['TITLE'],
                'recruiter_id' => $row['RECRUITER_ID'],
                'company' => $row['COMPANY_NAME'],
                'date_applied' => strtotime($row['OPEN_DATE']),
                'status' => $row['STATUS']
            );
        }

        return $applications;

    }

    /**
     * Returns the number of new applications for jobs within the given industry and month.
     */
    public function industry_new_applications($industry, $month) {

        $result = $this->doQuery(sprintf("
              SELECT  COUNT(*)
                FROM  APPLICATION A,
                      JOB J
               WHERE  A.JOB_ID = J.JOB_ID
                 AND  J.INDUSTRY = '%s'
                 AND  A.OPEN_DATE >= '%s-01'
                 AND  A.OPEN_DATE <= '%s-31'",
            mysql_real_escape_string($industry),
            mysql_real_escape_string($month),
            mysql_real_escape_string($month)
        ));

        $row = mysql_fetch_row($result);

        return $row[0];

    }

    /**
     * Returns the total number of positions per given industry and month.
     */
    public function industry_total_positions($industry, $month) {

        $result = $this->doQuery(sprintf("
              SELECT  SUM(J.NUM_POSITIONS)
                FROM  JOB J
               WHERE  J.INDUSTRY = '%s'
                 AND  J.POST_DATE <= '%s-31'",
            mysql_real_escape_string($industry),
            mysql_real_escape_string($month)
        ));

        $row = mysql_fetch_row($result);

        return $row[0];

    }

    /**
     * Returns the number of filled positions per industry per month.
     */
    public function industry_filled_positions($industry, $month) {

         $result = $this->doQuery(sprintf("
              SELECT  COUNT(*) AS FILLED_POSITIONS
                FROM  JOB J,
                      APPLICATION A
               WHERE  A.JOB_ID = J.JOB_ID
                 AND  J.INDUSTRY = '%s'
                 AND  A.CLOSE_DATE <= '%s-31'
                 AND  A.STATUS = '5'",
            mysql_real_escape_string($industry),
            mysql_real_escape_string($month)
        ));

        $row = mysql_fetch_row($result);

        return $row[0];

    }

    /**
     * Returns all new jobs within a particular month and salary range.
     */
    public function salary_new_applications($min, $max, $month) {

        $query = sprintf("
              SELECT  COUNT(*)
                FROM  APPLICATION A,
                      JOB J
               WHERE  A.JOB_ID = J.JOB_ID
                 AND  J.MINIMUM_SALARY >= '%s'
                 AND  A.OPEN_DATE >= '%s-01'
                 AND  A.OPEN_DATE <= '%s-31' ",
            mysql_real_escape_string($min),
            mysql_real_escape_string($month),
            mysql_real_escape_string($month)
        );

        //unbounded salaray if max is less than 0
        if ($max >= 0) {
            $query .= sprintf("AND J.MINIMUM_SALARY <= '%s'",
                        mysql_real_escape_string($max));
        }

        $result = $this->doQuery($query);

        $row = mysql_fetch_row($result);

        return $row[0];
    }

    /*
     * Returns total positions within a particular month and salary range.
     */
    public function salary_total_positions($min, $max, $month) {

        $query = sprintf("
              SELECT  SUM(J.NUM_POSITIONS)
                FROM  JOB J
               WHERE  J.MINIMUM_SALARY >= '%s'
                 AND  J.POST_DATE <= '%s-31'",
            mysql_real_escape_string($min),
            mysql_real_escape_string($month)
        );

        //unbounded salaray if max is less than 0
        if ($max >= 0) {
            $query .= sprintf("AND J.MINIMUM_SALARY <= '%s'",
                        mysql_real_escape_string($max));
        }

        $result = $this->doQuery($query);

        $row = mysql_fetch_row($result);

        return $row[0];

    }

    /*
     * Returns the number of filled positions with a particular month and salary range.
     */
    public function salary_filled_positions($min, $max, $month) {

        $query = sprintf("
              SELECT  COUNT(*) AS FILLED_POSITIONS
                FROM  JOB J,
                      APPLICATION A
               WHERE  A.JOB_ID = J.JOB_ID
                 AND  J.MINIMUM_SALARY >= '%s'
                 AND  A.CLOSE_DATE <= '%s-31'
                 AND  A.STATUS = '5'",
            mysql_real_escape_string($min),
            mysql_real_escape_string($month)
        );
    
        //unbounded salaray if max is less than 0
        if ($max >= 0) {
            $query .= sprintf("AND J.MINIMUM_SALARY <= '%s'",
                        mysql_real_escape_string($max));
        }

        $result = $this->doQuery($query);

        $row = mysql_fetch_row($result);

        return $row[0];

    }

    /*
     * Changes a particular application's status.
     */
    public function change_application_status($application_id, $status) {

        $this->doQuery(sprintf("
                UPDATE  APPLICATION
                   SET  STATUS = '%s'
                 WHERE  APPLICATION_ID = '%s'",
            mysql_real_escape_string($status),
            mysql_real_escape_string($application_id)
        ));

    }

}

?>
