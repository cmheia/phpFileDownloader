<?php
//a:12:{s:4:"lang";s:2:"cn";s:9:"auth_pass";s:32:"d41d8cd98f00b204e9800998ecf8427e";s:4:"exit";s:4:"exit";s:15:"error_reporting";i:1;s:7:"journal";s:12:"archives.log";s:6:"folder";s:5:"file/";s:14:"down_part_size";i:65536;s:12:"cookie_login";s:12:"cookie_login";s:14:"set_time_limit";i:0;s:17:"cookie_cache_time";i:259200;s:13:"avliable_lang";a:2:{i:0;s:2:"cn";i:1;s:2:"en";}s:7:"version";s:3:"0.3";}
/*--------------------------------------------------
 | PHP FILE DOWNLOADER
 +--------------------------------------------------
 | phpFileDownloader 0.4
 | Copyright (c) 2015 cmheia
 | 原始代码来源于网络
 | Rebuild By cmheia
 | E-mail: me@dabria.net
 | URL: https://blog.dabria.net/
 | Last Changed: 2015-09-14
 +--------------------------------------------------
 | OPEN SOURCE CONTRIBUTIONS
 +--------------------------------------------------
 | phpFileManager 0.9.8
 | By Fabricio Seger Kolling
 | Copyright (c) 2004-2013 Fabrício Seger Kolling
 | E-mail: dulldusk@gmail.com
 | URL: http://phpfm.sf.net
 +--------------------------------------------------
 | It is the AUTHOR'S REQUEST that you keep intact the above header information
 | and notify him if you conceive any BUGFIXES or IMPROVEMENTS to this program.
 +--------------------------------------------------
 | LICENSE
 +--------------------------------------------------
 | Licensed under the terms of any of the following licenses at your choice:
 | - GNU General Public License Version 2 or later (the "GPL");
 | - GNU Lesser General Public License Version 2.1 or later (the "LGPL");
 | - Mozilla Public License Version 1.1 or later (the "MPL").
 | You are not required to, but if you want to explicitly declare the license
 | you have chosen to be bound to when using, reproducing, modifying and
 | distributing this software, just include a text file titled "LEGAL" in your version
 | of this software, indicating your license choice. In any case, your choice will not
 | restrict any recipient of your version of this software to use, reproduce, modify
 | and distribute this software under any of the above licenses.
 +--------------------------------------------------
 | CONFIGURATION AND INSTALATION NOTES
 +--------------------------------------------------
 | This program does not include any instalation or configuration
 | notes because it simply does not require them.
 | Just throw this file anywhere in your webserver and enjoy !!
 +--------------------------------------------------
*/

// session_id($_COOKIE[$cookie_login]);
session_start(); // session_start() 函数必须位于 <html> 标签之前

$doc_root = str_replace('//','/',str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER["DOCUMENT_ROOT"]));
$my_self = $doc_root.$_SERVER["PHP_SELF"];

// +--------------------------------------------------
// | Config
// +--------------------------------------------------
$cfg = new config();
$cfg->load();

switch ($error_reporting){
	case 0: error_reporting(0); @ini_set("display_errors",0); break;
	case 1: error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR); @ini_set("display_errors",1); break;
	case 2: error_reporting(E_ALL); @ini_set("display_errors",1); break;
}

set_time_limit($set_time_limit);

// +--------------------------------------------------
// | Config Class
// +--------------------------------------------------
class config
{
	var $data;
	var $filename;
	function config()
	{
		global $my_self;
		$this->data = array(
			'lang' => 'cn', // 默认界面语言
			'auth_pass' => md5(''), // 默认登陆口令为空
			'exit' => 'exit', // 退出口令
			'error_reporting' => 1,
			'journal' => 'archives.log', // 日志文件
			'folder' => 'file/', // 下载目录
			'down_part_size' => 1024 * 64, // 下载分块大小
			'cookie_login' => 'cookie_login', // 暂未使用
			'set_time_limit' => 300, // 允许脚本运行的时间，单位为秒
			'cookie_cache_time' => 60*60*24*3, // 3 Days
			'avliable_lang' => array('cn', 'en'), // 可选语言
			'version' => '0.4'
			);
		$data = false;
		$this->filename = $my_self;
		if (file_exists($this->filename)) {
			$me_old = file($this->filename);
			$objdata = trim(substr($me_old[1], 2));
			if (strlen($objdata)) {
				$data = unserialize($objdata);
			} // strlen($objdata)
		} // file_exists($this->filename)
		if (is_array($data) && count($data) == count($this->data)) {
			$this->data = $data;
		} // is_array($data) && count($data) == count($this->data)
		else {
			$this->save();
		}
	}
	function save()
	{
		$objdata = "<?php".chr(10)."//".serialize($this->data).chr(10);
		if (strlen($objdata)) {
			if (file_exists($this->filename)) {
				$me_old = file($this->filename);
				if ($me_new = @fopen($this->filename, "w")) {
					@fputs($me_new, $objdata, strlen($objdata));
					for ($x = 2; $x < count($me_old); $x++) {
						@fputs($me_new, $me_old[$x], strlen($me_old[$x]));
					}
					@fclose($me_new);
				} // $me_new = @fopen($this->filename, "w")
			} // file_exists($this->filename)
		} // strlen($objdata)
	}
	function load() { // 把参数加载为一系列全局变量
		foreach ($this->data as $key => $val) {
			$GLOBALS[$key] = $val;
		}
	}
} // class config

// +--------------------------------------------------
// | Internationalization
// +--------------------------------------------------
function ml($tag)
{
	global $lang;

	// English
	$en['language'] = 'English';
	$en['version'] = 'Version';
	$en['url'] = 'File';
	$en['key'] = 'Pass';
	$en['new_pass'] = 'New pass';
	$en['old_pass'] = 'Old pass';
	$en['new_pass_again'] = 'New pass again';
	$en['login'] = 'login';
	$en['go'] = 'Go';
	$en['spantime'] = 'DownTime';
	$en['second_file_size'] = 's; file size';
	$en['ms_file_size'] = 'ms; file size';
	$en['byte'] = 'Byte';
	$en['complete'] = 'Download complete. Current time';
	$en['invalid_password'] = 'Invalid key!';
	$en['please_try_again'] = 'Please try again.';
	$en['title'] = 'Remote File Downloader';
	$en['sub_title'] = '<img class="twitter-emoji" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAKHSURBVDhPZVPPSxRhGH7m252ZXXfX1dJd02xDhDISCvIUBHYJFPOiUEbRtYNExy5WXjp28NB/0EUvEQVdg04lFUU/IESDNdefu67rOrOzMz3vN2spPfAyL8/3Pu+vbz4jIHAAm4+fwv22hNhZhZbhD5orvjyPvS8+rL4cjty/o7l96AQb0zOoF9ah0gm4XxdgxG34OxG0jhV00NZcFipZR1B1YJ3pgV+qIJJtw9GpSUTuNnc/3Hs7j8Ctwd/chorZMAxDcsPMuAgcBTefgDLJWCbq60UEu1V4S3l4K2tQ3vIaK8ZgmFFtfxEYUIk6VJNPv8ER+3GiEa1KjAyy3fqhINlK4CrE+8raxD+0KfqiEa1q6p9H8mIRXtFEUDNCMb92bwWq3dUm/sEziRWNaI3g0+0A1jJqiymUXmfgLDTJ+Oia+tEoFyI/fUpXtnt2kb6yCvNkGXA7meDdMGkqovzEOG810pAQviyTUDzbR5zj7inAk7MA9Bqo061wiXGLibjyKH2TycTEF07OJEZiGwhHcFfpMTBu4snMG3RmUzjRlUYqYeugcsXBr3wJy4Uy7k1eYpc1FmcnVoYJFmcCFJ4DkTiQsjEyPoeWZnYvt9foXH4LxaLFbeDF7BgzOuyiCmRHOUIHCY8nEu35uHY1B8cFEtxlKhma+MJdH83pmDCWGmoV7A6gfYgZd3RrN24NYGjwODa2WIiUmPhDl7sxcXMgbF9iRUPtv8f0cYJlfnNhKSBpwd2q4vP38C30n87CauWIO2zD4/XZx4Bzz/TZ4df48xGw+opJuIQoFxjl5gUeq3qcW9rOsHLvg5An/nvOcFh1ZRYoveePsh5yVhuQvsCZx1k9G3IawB8xOxT1Y9raeQAAAABJRU5ErkJggg==" draggable="false" alt="😍" />';
	$en['can_not_open_file'] = 'Can not open file';
	$en['can_not_write_log'] = 'Can not write file';
	$en['query_file'] = 'Query File';
	$en['query_time'] = 'Query Time';
	$en['file_not_writeable'] = 'file not writeable';
	$en['i_have_success_save'] = 'I have success save';
	$en['write_successful'] = 'Write successful!';
	$en['os_has_logged'] = 'The operating system has written to the log records.';
	$en['success_or_failure'] = 'Success or failure';
	$en['create_new_file'] = 'File does not exist; attempting to create;';
	$en['create_failure'] = 'Create Failed';
	$en['file_size'] = 'File Size';
	$en['unknown_length'] = 'Unknown length';
	$en['unknown_error'] = 'Unknown error';
	$en['downloaded'] = 'Have downloaded';
	$en['download_progress'] = 'Download progress';
	$en['warn_to_url'] = 'Must be an absolute address';
	$en['alert_url'] = 'Please check u input!';
	$en['pwd_pls'] = 'Enter the password pls!';
	$en['confirm_exit'] = 'Exit?';
	$en['already_exit'] = 'Already exit';
	$en['setpwd'] = 'Set new password';
	$en['new_pwd_miss'] = 'Different new pass';
	$en['cancel'] = 'Cancel';

	// Chinese
	$cn['language'] = 'Chinese';
	$cn['version'] = '版本';
	$cn['url'] = '链接';
	$cn['key'] = '密码';
	$cn['new_pass'] = '新密码';
	$cn['old_pass'] = '旧密码';
	$cn['new_pass_again'] = '再来一次新密码';
	$cn['login'] = '登陆';
	$cn['go'] = '开始下载';
	$cn['spantime'] = '总计耗时';
	$cn['second_file_size'] = '秒，文件大小';
	$cn['ms_file_size'] = '微秒，文件大小';
	$cn['byte'] = '字节';
	$cn['complete'] = '下载完成。现在时间';
	$cn['invalid_password'] = '密码无效！';
	$cn['please_try_again'] = '请重新输入。';
	$cn['title'] = '远程下载';
	$cn['sub_title'] = '<img class="twitter-emoji" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAKHSURBVDhPZVPPSxRhGH7m252ZXXfX1dJd02xDhDISCvIUBHYJFPOiUEbRtYNExy5WXjp28NB/0EUvEQVdg04lFUU/IESDNdefu67rOrOzMz3vN2spPfAyL8/3Pu+vbz4jIHAAm4+fwv22hNhZhZbhD5orvjyPvS8+rL4cjty/o7l96AQb0zOoF9ah0gm4XxdgxG34OxG0jhV00NZcFipZR1B1YJ3pgV+qIJJtw9GpSUTuNnc/3Hs7j8Ctwd/chorZMAxDcsPMuAgcBTefgDLJWCbq60UEu1V4S3l4K2tQ3vIaK8ZgmFFtfxEYUIk6VJNPv8ER+3GiEa1KjAyy3fqhINlK4CrE+8raxD+0KfqiEa1q6p9H8mIRXtFEUDNCMb92bwWq3dUm/sEziRWNaI3g0+0A1jJqiymUXmfgLDTJ+Oia+tEoFyI/fUpXtnt2kb6yCvNkGXA7meDdMGkqovzEOG810pAQviyTUDzbR5zj7inAk7MA9Bqo061wiXGLibjyKH2TycTEF07OJEZiGwhHcFfpMTBu4snMG3RmUzjRlUYqYeugcsXBr3wJy4Uy7k1eYpc1FmcnVoYJFmcCFJ4DkTiQsjEyPoeWZnYvt9foXH4LxaLFbeDF7BgzOuyiCmRHOUIHCY8nEu35uHY1B8cFEtxlKhma+MJdH83pmDCWGmoV7A6gfYgZd3RrN24NYGjwODa2WIiUmPhDl7sxcXMgbF9iRUPtv8f0cYJlfnNhKSBpwd2q4vP38C30n87CauWIO2zD4/XZx4Bzz/TZ4df48xGw+opJuIQoFxjl5gUeq3qcW9rOsHLvg5An/nvOcFh1ZRYoveePsh5yVhuQvsCZx1k9G3IawB8xOxT1Y9raeQAAAABJRU5ErkJggg==" draggable="false" alt="😍" title="带有爱慕眼睛的表情" aria-label="表情符号： 带有爱慕眼睛的表情" />';
	$cn['can_not_open_file'] = '不能打开文件';
	$cn['can_not_write_log'] = '不能写入文件';
	$cn['query_file'] = '链接';
	$cn['query_time'] = '下载时间';
	$cn['file_not_writeable'] = '文件不可写入';
	$cn['i_have_success_save'] = '成功地将';
	$cn['write_successful'] = '本次下载已记录。';
	$cn['os_has_logged'] = '此次操作已记录。';
	$cn['success_or_failure'] = '写入失败';
	$cn['create_new_file'] = '日志文件不存在，新建。';
	$cn['create_failure'] = '创建失败！';
	$cn['file_size'] = '文件大小';
	$cn['unknown_length'] = '未知';
	$en['unknown_error'] = '未知错误';
	$cn['downloaded'] = '已经下载';
	$cn['download_progress'] = '下载进度';
	$cn['warn_to_url'] = '请输入完整链接。例如 http://www.example.com/file.txt';
	$cn['alert_url'] = '请认真填写下载链接！';
	$cn['pwd_pls'] = '请输入密码！';
	$cn['confirm_exit'] = '真的要退出吗？';
	$cn['already_exit'] = '您已退出';
	$cn['setpwd'] = '设置新密码';
	$cn['new_pwd_miss'] = '两次输入的新密码不同';
	$cn['cancel'] = '取消';

	$lang_ = $$lang; // 把变量$lang的值作为变量名
	if (isset($lang_[$tag])) {
		return $lang_[$tag];
	} // isset($lang_[$tag])
	else {
		// return "[$tag]"; // So we can know what is missing
		return $en[$tag];
	}
} // function et($tag)

class runtime
{
	var $StartTime = 0;
	var $StopTime = 0;
	function get_microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float) $usec + (float) $sec);
	}
	function start()
	{
		$this->StartTime = $this->get_microtime();
	}
	function stop()
	{
		$this->StopTime = $this->get_microtime();
	}
	function spent($m)
	{
		return round(($this->StopTime - $this->StartTime) * (($m=='1')?1000:1), 2);
	}
} // class runtime

function recoder($message)
{
	$result = "";
	global $journal;
	$logfile = $journal;

	if (!file_exists($logfile)) {
		$result .= ml('create_new_file');
		if (!fopen($logfile, 'w')) {
			$result .= ml('create_failure');
			return $result;
		} // !fopen($logfile, 'w')
	} // !file_exists($logfile)

	// 文件操作
	if (is_writable($logfile)) {
		if ($handle = fopen($logfile, 'a+')) {
			$message .= "\r\n";
			if (fwrite($handle, $message) === false) {
				$result .= ml('can_not_write_log') . $logfile;
			} // @fwrite($handle, $message) === false
			else {
				$result .= ml('write_successful');
			}
			fclose($handle);
		} // $handle = fopen($logfile, 'a+')
		else {
			$result .= ml('can_not_open_file') . $logfile;
		}
	} // is_writable($logfile)
	else {
		$result .= ml('file_not_writeable');
	}
	return $result;
} // function recoder($message)

if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
	$newlang = isset($_POST['newlang'])? $_POST['newlang']: null;
	if ($newlang != null && in_array($newlang, $avliable_lang, true)) {
		if (isset($_POST['fromhash']) && $_POST['fromhash'] == $_SESSION['fromhash']) {
			$lang = $newlang;
			$cfg->data['lang'] = $newlang;
			$cfg->save(); // 修改语言
		} else {
			// echo var_dump($newlang);
			die(ml('unknown_error'));
		}
	} // $newlang != null && array_key_exists($newlang, $avliable_lang) && isset($_POST['fromhash']) && $_POST['fromhash'] == $_SESSION['fromhash']
} // isset($_SESSION['login']) && $_SESSION['login'] == true

if (isset($_POST['fromhash']) && $_POST['fromhash'] == $_SESSION['fromhash']) {

	// 登录
	if (isset($_POST['key']) && md5($_POST['key']) == $auth_pass) {
		$_SESSION['login'] = true;
		setcookie("loggedon", $auth_pass, 0, "/");
		// $debug_1 .= 'login';
	} // isset($_POST['key']) && md5($_POST['key']) == $auth_pass

	// 退出
	if ((isset($_POST['url']) && $_POST['url'] == $exit) || (isset($_POST['url']) && md5($_POST['url']) == $exit)) {
		setcookie($cookie_login, '', time()-$cookie_cache_time);
		$_SESSION['login'] = false;
		// $debug_1 .= 'logout';
	} // isset($_POST['url']) && $_POST['url'] == $exit

	// 修改密码
	if (isset($_POST['oldkey']) && isset($_POST['newkey']) && md5($_POST['oldkey']) == $auth_pass) {
		$auth_pass = md5($_POST['newkey']);
		if ($cfg->data['auth_pass'] != $auth_pass) {
			$cfg->data['auth_pass'] = $auth_pass;
			$cfg->save(); // 修改密码
		} // $cfg->data['auth_pass'] != $auth_pass
	} // isset($_POST['oldkey']) && isset($_POST['newkey']) && md5($_POST['oldkey']) == $auth_pass
} // isset($_POST['fromhash']) && $_POST['fromhash'] == $_SESSION['fromhash']

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ml('title'); ?></title>
<script type="text/javascript">
function $(obj)
{
	return document.getElementById(obj);
}
<?php
if ($_SESSION['login'] == true) {
?>
// 控制地址长度
function query()
{
	if (input_form.url.value.length < 4)
	{
		if (input_form.url.value.length > 0)
		{
			alert(<?php echo "\"", ml('alert_url'), "\""; ?>);
			input_form.url.focus();
			$("infotable").style.display = "none";
		}
		else
		{
			if (confirm("<?php echo ml('confirm_exit');?>"))
			{
				input_form.url.value = "exit";
				input_form.submit();
			}
		}
	}
	else
	{
		input_form.submit();
	}
}
// 文件长度
var filesize=0;
// 显示文件长度
function setFileSize(len)
{
	filesize=len;
	$("filesize").innerHTML=len;
	$("infotable").style.display = "";
}
// 显示已下载量并计算百分比
function setDownloaded(len)
{
	$("downloaded").innerHTML=len;
	if (filesize>0)
	{
		var percent=Math.round(len*100/filesize);
		$("progressbar").style.width=(percent+"%");
		if (percent>0)
		{
			$("progressbar").innerHTML=percent+"%";
			$("progresstext").innerHTML="";
		}
		else
		{
			$("progresstext").innerHTML=percent+"%";
		}

	}
}
// 设置语言
function setlang()
{
	if (confirm("Switch to new language: "+lang_form.newlang.value))
	{
		lang_form.submit();
	}
}
function autoresize()
{
	$("input_form").style.display="";
	$("url").style.width=$("query").parentNode.clientWidth*0.8-$("query").clientWidth*3+"px";
}
<?php
} // $_SESSION['login'] == true
else {
?>
// 登陆
function login()
{
	login_form.submit();
}
function check_new_pwd()
{
	if ($("newkey_again").value==$("newkey").value)
	{
		$("setpwd").onclick=setnewpwd;
		$("setpwd").innerHTML="<?php echo ml('setpwd'); ?>";
	}
	else
	{
		$("setpwd").innerHTML="<?php echo ml('new_pwd_miss'); ?>";
		$("setpwd").onclick=function(){alert("<?php echo ml('new_pwd_miss'); ?>");};
	}
}
function setnewpwd()
{
	reset_form.submit();
}
function reset(s)
{
	if (s == 1)
	{
		$("login_form").style.display = "none";
		$("reset_form").style.display = "";
	}
	else
	{
		$("login_form").style.display = "";
		$("reset_form").style.display = "none";
	}
}
function autoresize()
{
	$("key").style.width = $("query").parentNode.clientWidth*0.5-$("query").clientWidth*2+"px";
}
<?php
} // $_SESSION['login'] == false
?>
window.onload = autoresize;
window.onresize = autoresize;
</script>
<style type="text/css">
* {
font-family: "Microsoft YaHei", "WenQuanYi Micro Hei", "WenQuanYi Micro Hei", Helvetica, Arial, sans-serif, sans-serif;
}
body {
font-size:16px;
margin:0;
padding:0;
}
input {
border:1px solid #447900;
margin:0 0;
padding:2px 4px;
vertical-align:middle;
height:26px;
font-size:16px;
color:#0000FF;
}
a {
color: #337ab7;
text-decoration:none;
}
.btn {
display:inline-block;
margin-bottom:0;
font-size:14px;
font-weight:400;
line-height:1.428571;
text-align:center;
white-space:nowrap;
vertical-align:middle;
-ms-touch-action:manipulation;
touch-action:manipulation;
cursor:pointer;
-webkit-user-select:none;
-moz-user-select:none;
-ms-user-select:none;
user-select:none;
background-image:none;
border:1px solid transparent;
border-radius:4px;
padding:6px 12px;
}
.btn.focus,.btn:focus,.btn:hover {
color:#333;
text-decoration:none;
}
.btn-default {
color:#333;
background-color:#fff;
border-color:#ccc;
}
.btn-default.focus,.btn-default:focus {
color:#333;
background-color:#e6e6e6;
border-color:#8c8c8c;
}
.btn-default:hover {
color:#333;
background-color:#e6e6e6;
border-color:#adadad;
}
table.i {
border:3px solid #cccccc;border-collapse:collapse;
}
th {
width:1px;
white-space:nowrap;
padding:2px 12px;
}
td.i {
padding:2px 12px;
}
div.footer {
width:80%;
margin:auto;
padding:10px;
}
span.info {
color:#FF0000;
}
span.sum {
color:#0000FF;
}
div.progressbar {
float:left;
width:1px;
text-align:center;
color:#FFFFFF;
background-color:#0066CC
}
div.progresstext {
color:#FF0000;
float:left;
}
div.lang {
position:fixed;
top:0;
right:0;
border:1px solid #cccccc;
border-radius:4px;
margin:10px;
padding:3px;
}
div.center {
text-align:center;
}
</style>
</head>

<body>
<div class="title">
	<h1 align="center"><?php echo ml('title'); ?><sup><?php echo ml('sub_title'); ?></sup></h1>
</div>

<?php
if ($_SESSION['login'] == true) {
?>
<form method="post" name="input_form" id="input_form" style="display:none;">
	<div class="center">
		<input name="fromhash" value="<?php echo $_SESSION['fromhash']; ?>" style="display:none" />
		<?php echo ml('url'); ?>: <input name="url" id="url" type="text" placeholder="<?php echo ml('warn_to_url'); ?>" />
		<a href="javascript:" class="btn btn-default" id="query" onclick="query()"><?php echo ml('go'); ?></a>
	</div>
<br />
</form>
<table border="1" width="80%" align="center" class="i" id="infotable" style="display:none;">
	<tr>
		<th><?php echo ml('file_size'); ?></th>
		<td class="i"><span id="filesize" class="info"><?php echo ml('unknown_length'); ?></span> <?php echo ml('byte'); ?></td>
	</tr>
	<tr>
		<th><?php echo ml('downloaded'); ?></th><td class="i"><span id="downloaded" class="info">0</span> <?php echo ml('byte'); ?></td>
	</tr>
	<tr>
		<th><?php echo ml('download_progress'); ?></th><td class="i"><div id="progressbar" class="progressbar"></div><div id="progresstext" class="progresstext">NaN</div></td>
	</tr>
</table>

<div class="lang">
	<form method="post" name="lang_form" id="lang_form" align="center">
		<select name="newlang">
			<option value="cn">简体中文</option>
			<option value="en">English</option>
		</select>
		<a href="javascript:" class="btn btn-default" id="lang" onclick="setlang()">SELECT</a>
		<input name="fromhash" value="<?php echo $_SESSION['fromhash']; ?>" style="display:none" />
	</form>
</div>

<?php
	$url = isset($_POST['url'])? $_POST['url']: null;
	if ($url == null) {
		// $alert = "<script>alert('" . $language['alert_url'] ."');</script>";
		// die($alert);
	} // $url == null
	else {
		// 开始计时
		$runtime = new runtime;
		$runtime->start();

		// 检查下载目录
		if (!is_dir($folder)) {
			mkdir($folder, 0777);
		}

		// 开始下载
		$remote_file = fopen($url, "rb");
		if ($remote_file) {
			// 获取文件大小
			$filesize = -1;
			$headers  = get_headers($url, 1);
			if (array_key_exists("Content-Length", $headers)) {
				$filesize = $headers["Content-Length"];
			} else {
				$filesize = 0;
			} // array_key_exists("Content-Length", $headers)

			// 不是所有的文件都会先返回大小的，
			// 有些动态页面不先返回总大小，这样就无法计算进度了
			if ($filesize != -1) {
				echo "<script>setFileSize($filesize);</script>"; // 前台显示文件大小
			} // $filesize != -1

			$new_file = $folder . basename($url);
			$local_file  = fopen($new_file, "wb");
			$total_len   = 0;
			$current_len = 0;
			if ($local_file) {
				while (!feof($remote_file)) {
					$current_part = fread($remote_file, $down_part_size); // 分块下载
					$current_len  = strlen($current_part); // 本次下载的字节数
					$total_len   += $current_len; // 累计已经下载的字节数
					fwrite($local_file, $current_part, $current_len); // $down_part_size ?
					echo "<script>setDownloaded($total_len);</script>"; // 前台显示下载进度
					ob_flush();
					flush();
				} // !feof($remote_file)
			} // $local_file
		} // $remote_file

		if ($remote_file) {
			fclose($remote_file);
		} // $remote_file

		if ($local_file) {
			fclose($local_file);
		} // $local_file

		// 停止计时
		$runtime->stop();

		// 总结
		$dldone  = '<p><span class="info">' . ml('complete') . date("Y-m-d H:i:s") . '</span></p>';
		$summary = '<p>' . ml('spantime') . ': <span class="sum"> ' . $runtime->spent('0') . ' </span>' . ml('second_file_size') . ': <span class="sum"> ' . $filesize . ' </span>' . ml('byte') . '</p>';

		// 记录日志
		$logs = ml('query_file') . ': ' . $url . "\r\n" . ml('spantime') . ": " . $runtime->spent('1') . ml('ms_file_size') . ": " . $headers["Content-Length"] . ml('byte') . "\r\n" . ml('query_time') . ': ' . date("Y-m-d H:i:s") . "\r\n" . "\r\n";
		$record_result = recoder($logs);

		echo "<div class='footer'>", $summary, $dldone, $record_result, "</div>";
	} // $url != null
} // $_SESSION['login'] == true
else {
// 来路验证
srand(microtime(true) * 1000);
$fromhash = rand();
$session_id = session_id();
$_SESSION['fromhash'] = $fromhash;
setcookie($cookie_login, $fromhash, time()+$cookie_cache_time);
// $debug_1 .= 'login form';
?>
<form method="post" name="login_form" id="login_form">
	<div class="center">
		<input name="fromhash" value="<?php echo $fromhash; ?>" style="display:none" />
		<a href="javascript:" id="reset" onclick="reset(1)"><?php echo ml('key'); ?></a>: <input name="key" id="key" type="password" />
		<a href="javascript:" class="btn btn-default" id="query" onclick="login()"><?php echo ml('login'); ?></a>
<?php
if (isset($_POST['key']) && md5($_POST['key'])) {
	if ($auth_pass && $_POST['key'] != $exit) {
		echo '<p><span class="info">', ml('invalid_password'), ml('please_try_again'), ml('os_has_logged'), '</span></p>';
		$logs = date("Y-m-d H:i:s") . " key[" . $_POST['key'] . "]";
		recoder($logs);
	} // $auth_pass && $_POST['key'] != $exit
	else {
		echo '<p><span class="info">', ml('already_exit'), '</span></p>';
	}
} // isset($_POST['key']) && md5($_POST['key']) != $auth_pass
?>
	</div>
</form>

<form method="post" name="reset_form" id="reset_form" style="display:none">
	<input name="fromhash" value="<?php echo $fromhash; ?>" style="display:none" />

	<table align="center">
		<tr>
			<td><?php echo ml('old_pass'); ?></td>

			<td><input name="oldkey" id="oldkey" type="password" /></td>
		</tr>

		<tr>
			<td><?php echo ml('new_pass'); ?></td>

			<td><input name="newkey" id="newkey" type="password" oninput="check_new_pwd()" /></td>
		</tr>

		<tr>
			<td><?php echo ml('new_pass_again'); ?></td>

			<td><input id="newkey_again" type="password" oninput="check_new_pwd()" /></td>
		</tr>

		<tr>
			<td></td>

			<td><a href="javascript:" class="btn btn-default" id="setpwd" onclick="setnewpwd()" name="setpwd"><?php echo ml('setpwd'); ?></a>  <a href="javascript:" id="setpwd0" onclick="reset(0)" name="setpwd0"><?php echo ml('cancel'); ?></a></td>
		</tr>
	</table>
</form>

<?php
} // $_SESSION['login'] == false
?>
</body>
</html>
