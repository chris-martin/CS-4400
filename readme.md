# CS 4400 - Intro to Database Systems - Spring 2008

## Database build scripts

(run in order)

* createTables.sql
* populateLookupTables.sql
* populateDataTables.sql

## Webapp notes

### Session variables

    user_id     the id of the user who is logged in
    applicant   true iff the logged-in user is an applicant
    recruiter   true iff the logged-in user is a recruiter
    admin       true iff the logged-in user is an administrator

### Import architecture

Each page imports `lib.php`, which includes `functions.php` and `db.php`,
and does some initial setup (connects to the database, starts the session).

