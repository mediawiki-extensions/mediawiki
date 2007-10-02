<?xml version="1.0"?>
<package packagerversion="1.4.0a9" version="2.0" 
	xmlns="http://pear.php.net/dtd/package-2.0" 
	xmlns:tasks="http://pear.php.net/dtd/tasks-1.0" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

<!-- this file is the same as the one provided in the download package i.e. $package.tgz -->

 <!-- customize here -->
 <channel>jldupont.googlecode.com/svn</channel>	
 <name>$package</name>
 
 <summary>$summary</summary>
 <description>$description</description>

<!-- list of lead maintainers {{ --> 
 <lead>
  <name>$name</name>
  <user>$username</user>		<!-- e.g. jldupont -->
  <email>$email</email>
  <active>yes</active>
 </lead>
<!-- }} -->

 <date>$date</date>
 <time>$time</time>

 <version>
  <release>$release</release>
  <api>$api_release</api>
 </version>
 
 <stability>
  <release>$stability</release>
  <api>$api_stability</api>
 </stability>

 <license uri="http://www.php.net/license">PHP License</license>
 
 <notes>$notes</notes>
 
 <contents>
  <dir name="/">

	<!-- file list begin {{ -->
	<!-- md5sum attribute is optional -->
   <file baseinstalldir="Auth/HTTP" md5sum="9b7fe356f6793ccab49df1e3e39e2c6e" name="tests/sample.sql" role="test" />
    <!-- }} -->
  </dir>
 </contents>
 
 <dependencies>
  <required>

   <php>
    <min>5.0.0</min>
    <max>6.0.0</max>
   </php>

   <pearinstaller>
    <min>1.4.0a2</min>
   </pearinstaller>
   
   <package>
    <name>Auth</name>
    <channel>pear.php.net</channel>
    <min>1.2.0</min>
   </package>

  </required>
 </dependencies>

 <phprelease>
  <installconditions>
   <os>
    <name>*</name>
   </os>
  </installconditions>
  <filelist>
   <install as="HTTP.php" name="Auth_HTTP.php" />
  </filelist>
 </phprelease>

<!-- 
CHANGELOG 
Just a copy of the previous <release> sections
-->

 <changelog>
 
 <!-- foreach release {{ -->
  <release>
   <version>
    <release>$release_old1</release>
    <api>$api_release_old1</api>
   </version>
   <stability>
    <release>$stability_old1</release>
    <api>$api_stability_old1</api>
   </stability>
   <date>$date_old1</date>
   <license uri="http://www.php.net/license">PHP License</license>
   <notes>$notes_old1</notes>
  </release>

<!-- }} -->

 </changelog>

</package>
