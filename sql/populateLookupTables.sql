
-- Populate citizenship lookup table
INSERT INTO CITIZENSHIP_LU (ID, NAME)
  VALUES (1, 'United States'),
         (2, 'Canada'),
         (3, 'Mexico'),
         (4, 'Other');

-- Populate position type lookup table
INSERT INTO POSITION_TYPE_LU (ID, NAME)
  VALUES (1, 'Full time'),
         (2, 'Part time'),
         (3, 'Internship'),
         (4, 'Temporary');

-- Populate industry lookup table
INSERT INTO INDUSTRY_LU (ID, NAME)
  VALUES (1, 'Accounting'),
         (2, 'Computers'),
         (3, 'Education'),
         (4, 'Fashion'),
         (5, 'Insurance');

-- Populate degree lookup table
INSERT INTO DEGREE_LU (ID, NAME)
  VALUES (1, 'High School or below'),
         (2, 'Bachelor'),
         (3, 'Master'),
         (4, 'PhD');

-- Populate application status lookup table
INSERT INTO APPLICATION_STATUS_LU (ID, NAME)
  VALUES (1, 'In test process'),
         (2, 'In interview process'),
         (3, 'In decision process'),
         (4, 'Declined'),
         (5, 'Accepted');

-- Populate test type lookup table
INSERT INTO TEST_TYPE_LU (ID, NAME)
  VALUES (1, 'GRE'),
         (2, 'GMAT'),
         (3, 'MCAT'),
         (4, 'STAR'),
         (5, 'CERT');


