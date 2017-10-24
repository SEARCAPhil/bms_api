<?php
namespace Helpers;
class CleanStr{
	public function __construct(){}
	public function clean($str,$nullify=true){
		return ltrim(strip_tags(htmlentities(htmlspecialchars(utf8_encode($str)))));
	}
}

?>