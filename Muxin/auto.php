<?php
	//自动加载定义
	spl_autoload_register(function( $class ){
		$file_path = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.str_replace('\\',DIRECTORY_SEPARATOR,$class).'.php';
		if( file_exists( $file_path ) ){
			require_once( $file_path );
			return true;
		}
		return false;
	});
?>