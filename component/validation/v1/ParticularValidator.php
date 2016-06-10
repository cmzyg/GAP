<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 20/10/14
 * Time: 16:50
 */

namespace component\validation\v1;


class ParticularValidator {
    
	protected static $regex = array(
			'numeric' 	=> '/^[\d]+$/',
			'pp'		=> '/(^[^,]+,[^,]+,[A-Z@]{3},[A-Z]{2,3},[^\ ]+)|(^[^,]+,,[A-Z]{3})/',
			'hash'		=> '/^[\da-f]{128}$/',
			'boolean'   => '/^\A[01]{1}\Z/',
			'fprovider' => '/^\A[012]{1}\Z/',
			'freespin'  => '/^\A[01234]{1}\Z/',
			'ai'   		=> '/^(\A[\d]+,[\d]+,[\d.]+,[\d.]+,[\d.]+,[\d]+\Z)|(\A[\d]+,[\d]+,[^,]+,[\d.]+,[^,]+,[\d]+\Z)/',
			'loginc'    => '/([(\d)]*,.*,[\d\w]{0,}|[(\d)]+,.*)/',
		);

    public static function regex($type,$value)
    {
        if($type !== null && $value !== 'fun')
        {
            $result = preg_match(self::$regex[$type],$value);
            return $result;
        }
        else
        {
            return true;
        }
    }

} 