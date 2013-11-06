<?php
if (isset($_GET['act'])) {
	session_start();
	if ($_GET['act'] == "get_tree") {
		$main_var = "parol";
		include("../adldap/adLDAP.php"); 
		include("../ad_config.php");
		include("../ad_functions.php");
		$ob_type = (isset($_GET['type']))?$_GET['type']:NULL;
		if (isset($_GET['pNode'])) {
			$par_node = ($_GET['pNode']=="NULL")?$_GET['pNode']:explode("__",$_GET['pNode']);
			echo (build_tree($par_node, $ob_type));
		}
	}
	
}

?>