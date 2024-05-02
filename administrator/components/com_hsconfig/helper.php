<?php
/**
 * HsConfig Component Helper
 *
 * @license		GNU/GPL
 */

/**
 * HsConfig Component Helper
 */
class HsConfigHelper
{
	/**
	 * Determine if the id given represents the site configuration
	 *
	 * @param mixed $id
	 * @return true if id is for the site configuration, otherwise false
	 */
	function isSiteConfig( $id )
	{
		return ($id == -1);
	}

	function isPublished( $id )
	{
		jimport('joomla.filesystem.file');

		if (!JFile::exists( HsConfigHelper::createCssName($id)))
		{
			return false;
		}
		if (!JFile::exists( HsConfigHelper::createJsName($id)))
		{
			return false;
		}
		return true;
	}

	/**
	 * create a CSS stylesheet name based upon the id given
	 *
	 * @param mixed $id
	 * @return full path file name
	 */
	function createCssName( $id )
	{
		$fname = JPATH_ROOT.DS.'hsconfig'.DS.'css'.DS;

		if (HsConfigHelper::isSiteConfig($id) )
		{
			$fname .=  'highslide-sitestyles.css';
		}
		else
		{
			$fname .= 'highslide-article-' . $id . '-styles.css';
		}
		return $fname;
	}

	function createJsName( $id )
	{
		$fname = JPATH_ROOT.DS.'hsconfig'.DS.'js'.DS;

		if (HsConfigHelper::isSiteConfig($id) )
		{
			$fname .=  'highslide-sitesettings.js';
		}
		else
		{
			$fname .= 'highslide-article-' . $id . '-settings.js';
		}
		return $fname;
	}
}