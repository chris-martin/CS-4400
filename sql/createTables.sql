--
-- Database: `CareerWorks`
--

-- --------------------------------------------------------

--
-- Table structure for table `ADMINISTRATOR`
--

CREATE TABLE `ADMINISTRATOR` (
  `ADMIN_ID` int(11) NOT NULL auto_increment,
  `PASSWORD` varchar(32) NOT NULL,
  PRIMARY KEY  (`ADMIN_ID`),
  UNIQUE KEY `PASSWORD` (`PASSWORD`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `APPLICANT`
--

CREATE TABLE `APPLICANT` (
  `USER_ID` int(11) NOT NULL,
  `PHONE` char(10) default NULL,
  `HIGHEST_DEGREE` int(11) default NULL,
  `YEARS_EXPERIENCE` int(11) default NULL,
  `CITIZENSHIP` int(11) default NULL,
  `BIRTH_YEAR` int(11) default NULL,
  `DESCRIPTION` varchar(500) default NULL,
  PRIMARY KEY  (`USER_ID`),
  KEY `CITIZENSHIP` (`CITIZENSHIP`),
  KEY `HIGHEST_DEGREE` (`HIGHEST_DEGREE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `APPLICATION`
--

CREATE TABLE `APPLICATION` (
  `APPLICATION_ID` int(11) NOT NULL auto_increment,
  `APPLICANT_ID` int(11) NOT NULL,
  `JOB_ID` int(11) NOT NULL,
  `TEST_SCORE` int(11) default NULL,
  `STATUS` int(11) NOT NULL default '1',
  `OPEN_DATE` date NOT NULL,
  `CLOSE_DATE` date default NULL,
  PRIMARY KEY  (`APPLICATION_ID`),
  UNIQUE KEY `APPLICANT_ID` (`APPLICANT_ID`,`JOB_ID`),
  KEY `STATUS` (`STATUS`),
  KEY `JOB_ID` (`JOB_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `APPLICATION_STATUS_LU`
--

CREATE TABLE `APPLICATION_STATUS_LU` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(32) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `CITIZENSHIP_LU`
--

CREATE TABLE `CITIZENSHIP_LU` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(32) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `CUSTOMER`
--

CREATE TABLE `CUSTOMER` (
  `USER_ID` int(11) NOT NULL auto_increment,
  `PASSWORD` varchar(32) NOT NULL,
  `EMAIL` varchar(220) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  PRIMARY KEY  (`USER_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `DEGREE_LU`
--

CREATE TABLE `DEGREE_LU` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(32) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `INDUSTRY_LU`
--

CREATE TABLE `INDUSTRY_LU` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(32) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `JOB`
--

CREATE TABLE `JOB` (
  `JOB_ID` int(11) NOT NULL auto_increment,
  `POSTED_BY` int(11) NOT NULL,
  `POST_DATE` date NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `DESCRIPTION` varchar(500) default NULL,
  `INDUSTRY` int(11) default NULL,
  `MINIMUM_SALARY` int(11) NOT NULL,
  `TEST_TYPE` int(11) default NULL,
  `MIN_TEST_SCORE` int(11) default NULL,
  `EMAIL` varchar(220) NOT NULL,
  `PHONE` char(10) NOT NULL,
  `FAX` char(10) default NULL,
  `NUM_POSITIONS` int(11) NOT NULL default '0',
  `ACTIVE` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`JOB_ID`),
  KEY `POSTED_BY` (`POSTED_BY`),
  KEY `INDUSTRY` (`INDUSTRY`),
  KEY `TEST_TYPE` (`TEST_TYPE`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `JOB_POSITION_TYPE`
--

CREATE TABLE `JOB_POSITION_TYPE` (
  `JOB_ID` int(11) NOT NULL,
  `POSITION_TYPE` int(11) NOT NULL,
  KEY `JOB_ID` (`JOB_ID`),
  KEY `POSITION_TYPE` (`POSITION_TYPE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `POSITION_TYPE_LU`
--

CREATE TABLE `POSITION_TYPE_LU` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(32) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `RECRUITER`
--

CREATE TABLE `RECRUITER` (
  `USER_ID` int(11) NOT NULL,
  `COMPANY_NAME` varchar(255) NOT NULL,
  `PHONE` char(10) default NULL,
  `FAX` char(10) default NULL,
  `WEBSITE` varchar(255) default NULL,
  `DESCRIPTION` varchar(500) default NULL,
  PRIMARY KEY  (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TEST_TYPE_LU`
--

CREATE TABLE `TEST_TYPE_LU` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(32) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `APPLICANT`
--
ALTER TABLE `APPLICANT`
  ADD CONSTRAINT `APPLICANT_ibfk_7` FOREIGN KEY (`USER_ID`) REFERENCES `CUSTOMER` (`USER_ID`),
  ADD CONSTRAINT `APPLICANT_ibfk_8` FOREIGN KEY (`HIGHEST_DEGREE`) REFERENCES `DEGREE_LU` (`ID`),
  ADD CONSTRAINT `APPLICANT_ibfk_9` FOREIGN KEY (`CITIZENSHIP`) REFERENCES `CITIZENSHIP_LU` (`ID`);

--
-- Constraints for table `APPLICATION`
--
ALTER TABLE `APPLICATION`
  ADD CONSTRAINT `APPLICATION_ibfk_2` FOREIGN KEY (`APPLICANT_ID`) REFERENCES `APPLICANT` (`USER_ID`),
  ADD CONSTRAINT `APPLICATION_ibfk_3` FOREIGN KEY (`JOB_ID`) REFERENCES `JOB` (`JOB_ID`),
  ADD CONSTRAINT `APPLICATION_ibfk_4` FOREIGN KEY (`STATUS`) REFERENCES `APPLICATION_STATUS_LU` (`ID`);

--
-- Constraints for table `JOB`
--
ALTER TABLE `JOB`
  ADD CONSTRAINT `JOB_ibfk_3` FOREIGN KEY (`POSTED_BY`) REFERENCES `RECRUITER` (`USER_ID`),
  ADD CONSTRAINT `JOB_ibfk_4` FOREIGN KEY (`INDUSTRY`) REFERENCES `INDUSTRY_LU` (`ID`),
  ADD CONSTRAINT `JOB_ibfk_5` FOREIGN KEY (`TEST_TYPE`) REFERENCES `TEST_TYPE_LU` (`ID`);

--
-- Constraints for table `JOB_POSITION_TYPE`
--
ALTER TABLE `JOB_POSITION_TYPE`
  ADD CONSTRAINT `JOB_POSITION_TYPE_ibfk_2` FOREIGN KEY (`JOB_ID`) REFERENCES `JOB` (`JOB_ID`),
  ADD CONSTRAINT `JOB_POSITION_TYPE_ibfk_3` FOREIGN KEY (`POSITION_TYPE`) REFERENCES `POSITION_TYPE_LU` (`ID`);

--
-- Constraints for table `RECRUITER`
--
ALTER TABLE `RECRUITER`
  ADD CONSTRAINT `RECRUITER_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `CUSTOMER` (`USER_ID`);

