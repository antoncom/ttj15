<?php
/**
 * SEF module for Joomla! - URL caching system
 * *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007
 * @package     sh404SEF-15
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: shCache.php 658 2008-08-08 09:42:36Z silianacom-svn $
 *
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

if (!defined('sh404SEF_FRONT_ABS_PATH')) {
  define('sh404SEF_FRONT_ABS_PATH', str_replace('\\','/',dirname(__FILE__)).'/');
}
if (!defined('sh404SEF_ABS_PATH')) {
  define('sh404SEF_ABS_PATH', str_replace( '/components/com_sh404sef', '', sh404SEF_FRONT_ABS_PATH) );
}
if (!defined('sh404SEF_ADMIN_ABS_PATH')) {
  define('sh404SEF_ADMIN_ABS_PATH', sh404SEF_ABS_PATH.'administrator/components/com_sh404sef/');
}

define('SH_CACHE_NON_SEF_URL', 0);
define('SH_CACHE_SEF_URL', 1);
define('SH_CACHE_URL_TYPE', 2);

define('SH_SHOW_CACHE_STATS', 0);

global $sefConfig;

// URL Cache management

global $shIsoCodeCache, $shLangNameCache;
global $shURLDiskCache, $shURLMemCache, $shURLCacheFileName, $shURLTotalCount, $shURLCacheCreationDate;
global $shURLCacheMisses, $shURLCacheHits, $shURLCacheMissesList;

$shURLCacheMisses = 0;
$shURLCacheMissesList = array();
$shURLCacheHits = 0;

$shIsoCodeCache = null;
$shLangNameCache = null;
$shURLDiskCache = null;
$shURLMemCache = null;
$shURLCacheFileName = sh404SEF_FRONT_ABS_PATH.'cache/shCacheContent.php';
$shURLTotalCount = 0;  // v 1.2.4.c added total cache size control
$shURLCacheCreationDate = null;

if (!empty($sefConfig->shUseURLCache))
register_shutdown_function('shWriteURLCacheToDisk');

function sh_var_export( $cache, $start) {
  // export content of array $cache, inserting a numeric key starting at $start
  $size = count($cache);
  if (empty($size)) return '';
  $ret = '';
  for ($i=0; $i<$size; $i++) {  // use for instead of foreach to reduce memory usage
    // new version, smaller RAM footprint
    $ret .= "\n".'$shURLDiskCache['.$start++.']=\''.$cache[$i].'\';';
  }
  // new version, less ram footprint

  return $ret;
}

function shWriteURLCacheToDisk() {
  global $shURLDiskCache, $shURLMemCache, $shURLCacheFileName, $sefConfig, $shURLCacheCreationDate;

  if (!count($shURLMemCache)) return; // nothing to do, no new URL to write to disk
  $now = time();
  if (!file_exists($shURLCacheFileName))
  $cache = '<?php // shCache : URL cache file for sh404SEF
//'.$sefConfig->version.'    
if (!defined(\'_JEXEC\')) die(\'Direct Access to this location is not allowed.\');
$shURLCacheCreationDate = '.$now.';'."\n";
  else {
    $cache = '<?php'."\n";
    // check cache TTL
    if (empty($shURLCacheCreationDate)){  // file exists, but creation date is missing : we are upgrading from a previous version
      $status = stat($shURLCacheFileName);  // lets's read from file status : use last change date as creation date
      if (!empty($status)) {
        $shURLCacheCreationDate = $status[9];
        $cache .= "\n".'$shURLCacheCreationDate='.$shURLCacheCreationDate.";\n";
      }
    }
    if (SH404SEF_URL_CACHE_TTL && mt_rand(1, SH404SEF_URL_CACHE_WRITES_TO_CHECK_TTL) == 1) { // probability = 1/SH404SEF_WRITES_TO_CLEAN_LOGS
      if (!empty($shURLCacheCreationDate)){  // if we have a valid creation date, check  TTL
        if (($now-$shURLCacheCreationDate) > SH404SEF_URL_CACHE_TTL*86400) { // cache must be cleared
          $GLOBALS['shURLDiskCache'] = array();
          unlink($shURLCacheFileName);
          $shURLCacheCreationDate = $now;
          $cache = '<?php // shCache : URL cache file for sh404SEF
//'.$sefConfig->version.'    
if (!defined(\'_JEXEC\')) die(\'Direct Access to this location is not allowed.\');
$shURLCacheCreationDate = '.$now.';'."\n";
        }
      }
    }
  }
  $count = count( $shURLDiskCache);
  $cache .= sh_var_export( $shURLMemCache, $count); // only need to write memory cache, ie: those URL added since last read of cache from disk
  $cache .= "\n".'?'.'>';
  $cacheFile=fopen( $shURLCacheFileName,'ab');
  if ($cacheFile) {
    fwrite( $cacheFile, $cache);
    fclose( $cacheFile);
  }
}

// fetch an URL from cache, return null if not found
function shGetSefURLFromCache($string, &$url) {
  global $sefConfig, $shURLCacheMisses, $shURLCacheHits, $shURLCacheMissesList;

  if (!$sefConfig->shUseURLCache) {
    $url = null;
    $shURLCacheMisses += 1;
    return sh404SEF_URLTYPE_NONE;
  }
  shLoadURLCache();
  $diskCacheSize = count($GLOBALS['shURLDiskCache']);
  $memCacheSize = count($GLOBALS['shURLMemCache']);
  if (empty($diskCacheSize) && empty($memCacheSize)) {
    $url = null;
    $shURLCacheMisses += 1;
    return sh404SEF_URLTYPE_NONE;
  }
  $string = htmlentities( $string, ENT_QUOTES);
  for ($i=0; $i<$diskCacheSize; $i++) {
    if (strpos($GLOBALS['shURLDiskCache'][$i], $string) !== false) {
      $tmp = explode('#', $GLOBALS['shURLDiskCache'][$i]);  // cache format : non-sef#sef#type
      if ($string == $tmp[0]) {
        $url = $tmp[1];
        _log('Retrieved SEF from disk cache : '.$url.' => '.html_entity_decode( $tmp[0], ENT_QUOTES).'('.$tmp[2].')');
        $shURLCacheHits += 1;
        return $tmp[2];
      }
    }
  }
  for ($i=0; $i<$memCacheSize; $i++) {
    if (strpos($GLOBALS['shURLMemCache'][$i], $string) !== false) {
      $tmp = explode('#', $GLOBALS['shURLMemCache'][$i]);  // cache format : non-sef#sef#type
      if ($string == $tmp[0]) {
        $url = $tmp[1];
        _log('Retrieved SEF from mem cache : '.$url.' => '.html_entity_decode( $tmp[0], ENT_QUOTES).'('.$tmp[2].')');
        $shURLCacheHits += 1;
        return $tmp[2];
      }
    }
  }
  $shURLCacheMisses += 1;
  $shURLCacheMissesList[] = $string;
  return sh404SEF_URLTYPE_NONE;
}

// fetch an URL from cache, return null if not found
function shGetNonSefURLFromCache($string, &$url) {
  global $sefConfig;

  if (!$sefConfig->shUseURLCache) {
    $url = null;
    return sh404SEF_URLTYPE_NONE;
  }
  shLoadURLCache();
  $diskCacheSize = count($GLOBALS['shURLDiskCache']);
  $memCacheSize = count($GLOBALS['shURLMemCache']);
  if (empty($diskCacheSize) && empty($memCacheSize)) {
    $url = null;
    return sh404SEF_URLTYPE_NONE;
  }
  for ($i=0; $i<$diskCacheSize; $i++) {
    if (strpos($GLOBALS['shURLDiskCache'][$i], $string) !== false) {
      $tmp = explode('#', $GLOBALS['shURLDiskCache'][$i]);  // cache format : non-sef#sef#type
      $nonSef = html_entity_decode( $tmp[0], ENT_QUOTES);
      if ($string == $tmp[1]) {
        $url = $nonSef;
        _log('Retrieved Non SEF from disk cache : '.$url.' => '.$tmp[1].'('.$tmp[2].')');
        return $tmp[2];
      }
    }
  }
  for ($i=0; $i<$memCacheSize; $i++) {
    if (strpos($GLOBALS['shURLMemCache'][$i], $string) !== false) {
      $tmp = explode('#', $GLOBALS['shURLMemCache'][$i]);  // cache format : non-sef#sef#type
      $nonSef = html_entity_decode( $tmp[0], ENT_QUOTES);
      if ($string == $tmp[1]) {
        $url = $nonSef;
        _log('Retrieved Non SEF from mem cache : '.$url.' => '.$tmp[1].'('.$tmp[2].')');
        return $tmp[2];
      }
    }
  }
  return sh404SEF_URLTYPE_NONE;
}

function shAddSefURLToCache( $nonSefURL, $sefURL, $URLType) {
  global $sefConfig, $shURLMemCache, $shURLTotalCount;
  if (!$sefConfig->shUseURLCache) return null;
  if ($shURLTotalCount >= $sefConfig->shMaxURLInCache) return null;  // v 1.2.4.c added total cache size control
  // Filter out non sef url which include &mosmsg, as I don't want to have a cache entry for every single msg
  // that can be thrown at me, including every 404 error
  if (strpos(strtolower($nonSefURL), '&mosmsg')) return null;
  $count = count($shURLMemCache);
  // new cache format : non-sef#sef#type
  $shURLMemCache[$count] = htmlentities( $nonSefURL, ENT_QUOTES).'#'.$sefURL.'#'.$URLType;
  _log('Adding to URL cache : '.$sefURL.' <= '.$nonSefURL);
  $shURLTotalCount++;  // v 1.2.4.c added total cache size control
  return true;
}

function shRemoveURLFromCache( $nonSefURLList) {
  global $sefConfig, $shURLMemCache, $shURLDiskCache, $shURLTotalCount;

  if (!$sefConfig->shUseURLCache || empty($nonSefURLList)) return null;
  $foundInDiskCache = false;
  $foundInMemCache = false;
  foreach ($nonSefURLList as $nonSefURL) {
    if (!empty($shURLMemCache)) {
      foreach ($shURLMemCache as $cacheItem) { // look up in memory cache
        $tmp = explode('#', $cacheItem);
        $cacheNonSef = html_entity_decode( $tmp[0], ENT_QUOTES);
        if ($cacheNonSef == $nonSefURL) {
          unset($cacheItem);
          $shURLTotalCount--;
          $foundInMemCache = true;
          break;
        }
      }
    }
    if (!empty($shURLDiskCache)) {
      foreach ($shURLDiskCache as $cacheItem) {  // look up disk cache
        $tmp = explode('#', $cacheItem);
        $cacheNonSef = html_entity_decode( $tmp[0], ENT_QUOTES);
        if ($cacheNonSef == $nonSefURL) {
          unset($cacheItem);
          $shURLTotalCount--;
          $foundInDiskCache = true;
          break;
        }
      }
    }
  }
  if ($foundInMemCache) {
    $shURLMemCache = array_values($shURLMemCache); // simply reindex mem cache
    return;
  }
  if ($foundInDiskCache) { // we need to remove these url from the disk cache file
    // to make it simpler, I simply rewrite the complete file
    $shURLMemCache = (empty($shURLMemCache) ?
    array_values($shURLDiskCache)
    :array_merge($shURLDiskCache, $shURLMemCache));
    $shURLDiskCache = array();  // don't need disk cache anymore, as all URL are in mem cache
    // so we remove both on disk cache and in memory copy of on disk cache
    if (file_exists(sh404SEF_FRONT_ABS_PATH.'cache/shCacheContent.php'))
    unlink(sh404SEF_FRONT_ABS_PATH.'cache/shCacheContent.php');
    // no need to write new URL list in disk file, as this will be done automatically at shutdown
  }
}

// load cached URL from disk into an array in memory
function shLoadURLCache() {
  global $shURLDiskCache, $shURLCacheFileName, $shURLTotalCount, $shURLMemCache;
  static $shDiskCacheLoaded = false;
  if (!$shDiskCacheLoaded) {
    _log('Cache not loaded - trying to load '.$shURLCacheFileName);
    if (file_exists( $shURLCacheFileName)) {
      $startMem = function_exists('memory_get_usage')? memory_get_usage():'unavailable';
      _log('Including cache file (mem = '.$startMem.')');
      $GLOBALS['shURLDiskCache'] = array();  // erase global, not local copy
      include($shURLCacheFileName);
      $endMem = function_exists('memory_get_usage')? memory_get_usage():'unavailable';
      $shDiskCacheLoaded = !empty($shURLDiskCache);
      $shURLTotalCount = !empty($shURLDiskCache) ? count($shURLDiskCache) : 0;
      _log('Cache file included : '.($startMem == 'unavailable' ? $startMem: $endMem-$startMem).' bytes used, '.$shURLTotalCount.' URLs');
    } else {
      $GLOBALS['shURLDiskCache'] = array();
      $shDiskCacheLoaded = false;
      _log('Cache file does not exists');
    }
  }
}

function shShowCacheStats() {
  global $shURLCacheMisses, $shURLCacheMissesList, $shURLCacheHits;

  $cacheTotal = $shURLCacheMisses+$shURLCacheHits;
  $out = 'Cache hits   : '. $shURLCacheHits . "  [".(int)(100*$shURLCacheHits/$cacheTotal) .']<br />';
  $out .= 'Cache misses : '. $shURLCacheMisses . "  [".(int)(100*$shURLCacheMisses/$cacheTotal). ']<br />';
  $out .=  'Cache total  : '. $cacheTotal . '<br />';
  $out .=  '<br /><br /><br />Misses list';
  foreach($shURLCacheMissesList as $url) {
    $out .=  '<pre>'.$url.'</pre><br />';
  }
  return $out;
}

?>
