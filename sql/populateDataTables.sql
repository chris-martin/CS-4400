--
-- Database: 'CareerWorks'
--

--
-- Dumping data for table 'ADMINISTRATOR'
--

INSERT INTO ADMINISTRATOR (ADMIN_ID, PASSWORD) VALUES
(1, 'pineapple');

--
-- Dumping data for table 'CUSTOMER'
--

INSERT INTO CUSTOMER (USER_ID, PASSWORD, EMAIL, NAME) VALUES
(1, 'test', 'mhluongo@gmail.com', 'Matt'),
(2, 'test', 'ch@aol.com', 'Chris Martin'),
(3, 'pie', 'chris.martin@gatech.edu', 'Christopher Martin'),
(4, 'test', 'mh@google.com', 'Matt'),
(5, 'test', 'povermanblahblah@somewebsite.com', 'Pamela'),
(6, 'bob', 'bob@bob.com', 'bob'),
(7, 'bob', 'bob1@bob.com', 'bob'),
(8, 'j', 'j@j.org', 'J');

--
-- Dumping data for table 'APPLICANT'
--

INSERT INTO APPLICANT (USER_ID, PHONE, HIGHEST_DEGREE, YEARS_EXPERIENCE, CITIZENSHIP, BIRTH_YEAR, DESCRIPTION) VALUES
(4, '678-567-04', 4, 5, 1, 1985, 'I''m pretty much the ideal, brilliant young candidate with something to prove... email for pics!!1!'),
(5, '706-699-04', 2, 20, 2, 1986, 'Well, I''m from canada, and I love... scarves!'),
(6, '1231231234', 2, 17, 2, 2003, 'bob');

--
-- Dumping data for table 'RECRUITER'
--

INSERT INTO RECRUITER (USER_ID, COMPANY_NAME, PHONE, FAX, WEBSITE, DESCRIPTION) VALUES
(1, 'HP', '678-904-32', '770-821-98', 'http://hewlett-packard.com', 'HP is a technology solutions provider to consumers, businesses and institutions globally. The companyâ€™s offerings span IT infrastructure, global services, business and home computing, and imaging & printing.'),
(2, 'Dell', '', '', 'http://dell.com', 'HP''s sworn enemy!'),
(3, 'Martin Co.', '345.132.12', '112.125.e', 'http://chris-martin.org', 'I''m a great guy.greefewef'),
(7, 'bob inc', '1231231234', '1231231234', 'http://www.bob.com', 'bob inc'),
(8, 'Kramerica', '4353454435', '34534543', 'http://', '');

--
-- Dumping data for table 'JOB'
--

INSERT INTO JOB (JOB_ID, POSTED_BY, POST_DATE, TITLE, DESCRIPTION, INDUSTRY, MINIMUM_SALARY, TEST_TYPE, MIN_TEST_SCORE, EMAIL, PHONE, FAX, NUM_POSITIONS, ACTIVE) VALUES
(1, 1, '2008-04-21', 'Provider of Services', 'Requires a lot of service.', 3, 44000, 5, 750, 'who@sam.edu', '452.246.58', NULL, 4, 1),
(2, 1, '2007-01-23', 'Applications Programmer', 'Programming for end-user desktop environments.', 2, 40000, NULL, NULL, 'mhluongo@gmail.com', '770-897-03', NULL, 4, 1),
(3, 2, '2005-02-02', 'Corporate Spy', '...<top secret>...', 2, 200000, 3, 100, 'ch@aol.com', '678-983-45', NULL, 2, 1),
(4, 3, '2008-03-02', 'Executive assistant', 'I need assistance with all sorts of fashion.  Requires expert knowledge of scarfery.', 4, 5000, NULL, NULL, 'ch.martin@gmail.com', '233.135.12', NULL, 11, 0),
(5, 2, '2008-04-21', 'Cubicle Construction Artist', 'We need talented, driven college graduates to design our newest office space cubicles.', 2, 12000, NULL, NULL, 'ch@aol.com', '770-634-38', NULL, 3, 1),
(6, 7, '2008-04-22', 'cool job', 'teach bobs', 3, 50, 4, 10, 'bob1@bob.com', '1231231234', '1231231234', 14, 1);

--
-- Dumping data for table 'APPLICATION'
--

INSERT INTO APPLICATION (APPLICATION_ID, APPLICANT_ID, JOB_ID, TEST_SCORE, STATUS, OPEN_DATE, CLOSE_DATE) VALUES
(1, 4, 2, NULL, 5, '2008-04-21', NULL),
(2, 4, 4, 332, 4, '2008-04-21', NULL),
(3, 5, 4, 34, 5, '2008-04-21', NULL),
(4, 5, 3, NULL, 1, '2008-04-21', NULL),
(5, 5, 1, NULL, 1, '2008-04-21', NULL),
(6, 5, 5, NULL, 1, '2008-04-21', NULL),
(7, 6, 6, NULL, 4, '2008-04-22', NULL);

--
-- Dumping data for table 'JOB_POSITION_TYPE'
--

INSERT INTO JOB_POSITION_TYPE (JOB_ID, POSITION_TYPE) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(3, 1),
(4, 1),
(5, 3),
(6, 1),
(6, 3);

