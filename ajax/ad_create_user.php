<?php
if (isset($_GET['act'])) {
	session_start();
	if (($_GET['act'] == "cr_user")
		and (isset($_GET["cr_user_surn"]))
		and (isset($_GET["cr_user_name"]))
		and (isset($_GET["cr_user_fullname"]))
		and (isset($_GET["cr_user_logon"]))
		and (isset($_GET["cr_user_email"]))
		and (isset($_GET["cr_user_pass"]))
		and (isset($_GET["cr_user_cont"]))
	) {
		$main_var = "parol";
		include("../adldap/adLDAP.php");
		include("../ad_config.php");
		include("../ad_functions.php");

		$cont=explode(" / ", $_GET["cr_user_cont"]);
		$attributes=array(
			"username"=>mb_convert_encoding($_GET["cr_user_logon"], "Windows-1252"),
			"firstname"=>mb_convert_encoding($_GET["cr_user_name"], "Windows-1251", "UTF-8"),
			"surname"=>iconv("UTF-8","Windows-1252//TRANSLIT",$_GET["cr_user_surn"]),
			"email"=>iconv("UTF-8","Windows-1252//TRANSLIT",$_GET["cr_user_email"]),
			"container"=>$cont,
			"enabled"=>1,
			"password"=>$_GET["cr_user_pass"],
			"display_name"=>$_GET["cr_user_fullname"]
		);
		//echo "cont=". $cont;
		$result = $adldap->user()->create($attributes);
		if ($result) {
			echo "true";
		}
		else {
			echo "false";
		}
	}
}
?>