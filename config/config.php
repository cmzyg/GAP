<?php
define("DEBUG_ON",true);

define("DBHOST", "masterdb-api.isoftbet.com:3309");
define("DBUSER", "devel");
define("DBPASSWORD", "devel4riva");
define("DBNAME", "770_mdpadmin");

define("DBHOST_CALLBACK", "masterdb-api.isoftbet.com:3309");
define("DBUSER_CALLBACK", "devel");
define("DBPASSWORD_CALLBACK", "devel4riva");
define("DBNAME_CALLBACK", "770_mdpadmin");

define("TABLE_PREFIX", "gameap_");

define("FOLDER", "");
define("DOC_ROOT","/private-devel/gap.isoftbet.com".FOLDER);
define("API_URL", "http://gap.isoftbet.com/".FOLDER);

define("APPLICATION", DOC_ROOT."/application/");
define("SYSTEM", DOC_ROOT."/system/");
define("CLIENT", DOC_ROOT."/client/");

define("WSDL_URL", API_URL."index.php?wsdl");

define("CLIENT_METHOD_DISPLAY", API_URL."client/display.php");
define("CLIENT_METHOD_CALLBACK", API_URL."client/callback.php");

define("MODULE_FOLDER", API_URL."modules/");
?>