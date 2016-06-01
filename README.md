CCIMS
--------
This file serves as simple documentation for the CCIMS (Creative Coder Individual Management System).

The CCIMS system includes methods to do the following:

- VIEW Records 
- CREATE new records
- EDIT current records 
- DELETE records


Usage
=========
- EDIT a user by clicking their username
- EMAIL the user by clicking on their email address (pre-filled Subject & Body)
- Clicking the user's favorite movie launches a movie search on IMDB.com in a new window
- DELETE a user by clicking the user's '[x]' button

**List of users / passwords:**

- `clay / clay123`
- `allen / allen123`
- `chad / chad123`
- `michael / michael123`
- `john / john123`
- `joe / joe123`

**URL:**

- http://localhost/ccims-clay-leeds/


Future enhancements
=======================

- improved security via usage of sqlEvilStrings PHP class (contains list of 'evil' strings)
- add user roles using a system enabling capability to assume all roles below a user's role id
  given the following 'editor' users can do everything except functions limited to 'admin'
  role, while an 'contributor' is limited to things that 'contributor', 'subscriber' and
  'simple' can access):
  * admin=100
  * admin-vendor=95
  * editor=75
  * editor-vendor=70
  * author=55
  * author-vendor=50
  * contributor=30
  * subscriber=20
  * simple=10
- actual pages and content management functions like categories and commenting!
- give error & ancillary pages a nice looking template!
- add a Footer
- add documentation


File system
===============
ccims database db export w/ ccims database & ccims.ccims_users table

- `ccims-clay-leeds/ccims.sql`

PHP Files for CMS System

- ccims-clay-leeds/css/ccims.css ~ CSS file
- ccims-clay-leeds/edit.php ~ Edit user info
- ccims-clay-leeds/home.php ~ Home page (does not require login)
- ccims-clay-leeds/index.php ~ Main page to view, delete & edit
- ccims-clay-leeds/login.php ~ login page
- ccims-clay-leeds/logout.php ~ logout page

PHP Classes

- ccims-clay-leeds/pdo_ccims_connect.php ~ PDO file for database connection
- ccims-clay-leeds/classes/class.phpmailer.php ~ PHPMailer for sqlEvilString
- ccims-clay-leeds/classes/class.serverVarsUtility.php ~ robust Server VARs utility
- ccims-clay-leeds/classes/class.sqlEvilStrings.php ~ enables preventing for SQL Injection

