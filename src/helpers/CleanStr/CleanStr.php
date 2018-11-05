<?php
namespace Helpers;
class CleanStr{
	public function __construct(){}
	public function clean($str,$nullify=true){
		return ltrim(strip_tags(mb_convert_encoding($str, 'UTF-8')));
	}
}

?>