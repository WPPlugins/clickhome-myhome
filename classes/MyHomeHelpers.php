<?php

/**
 * The MyHomeHelpers class
 *
 * @package    MyHome
 * @subpackage Classes
 */

// Exit if the script is accessed directly
if(!defined('ABSPATH'))
  die;

// Do not attempt to redefine the class
if(class_exists('MyHomeHelpers'))
  return;

/**
 * The MyHomeHelpers class
 *
 * Handles MyHome helpers inserted anywhere in the site
 *
 * @since 1.6
 */
class MyHomeHelpers{
  /**
    * Find an object by key within array
    **/
  function findWhere($array, $index, $value) {
    foreach($array as $arrayInf) {
      if($arrayInf->{$index} == $value) {
        return $arrayInf;
      }
    }
    return null;
  }

  /**
    * Loose in_array function
    **/
  function like_in_array( $sNeedle , $aHaystack ) {
    foreach ($aHaystack as $sKey) {
      if( stripos( strtolower($sKey) , strtolower($sNeedle) ) !== false ) {
          return true;
      }
    }
    return false;
  }

  /**
    * Format a number into dollars (Windows/intl ext safe)
    **/
  function formatDollars($dollars, $showDecimals = true) {
    $decimals = $showDecimals===true ? 2 : ($showDecimals===false ? 0 : (isset(explode('.', $dollars)[1]) ? 2 : 0));
    $formatted = "$" . number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $dollars)), $decimals);
    return $dollars < 0 ? "({$formatted})" : "{$formatted}";
  }

  /**
    * Check if a string is json encoded
    **/
  public function is_json($str){ 
    return json_decode($str) != null;
  }

  /**
    * Check if a string is base64 encoded
    **/
  function is_base64($data) {
    //if(!is_numeric($data) && preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
    if ($data && !is_numeric($data) && base64_encode(base64_decode($data)) === $data) {
      return true;
    } else {
      return false;
    }
  }
}
