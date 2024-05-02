<?php
/**
 * Highslide JS Plugin
 *
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );


$mainframe->registerEvent( 'onAfterDispatch', 'onAfterDispatchHighslide' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.filecontent.file');
jimport( 'joomla.utilities.string');

function onAfterDispatchHighslide()
{
	$app      =& JFactory::getApplication();
	$document =& JFactory::getDocument();

	// check if site is active
	if (!($app->getName() == 'site' && is_a($document, 'JDocumentHTML'))) {
		return true;
	}

	// get plugin info
	$plugin =& JPluginHelper::getPlugin( 'content', 'highslide');
	if (count($plugin) == 0)
	{
		return true;
	}
	$params = new JParameter($plugin->params);

	// check whether plugin has been unpublished
	if (!$params->get('enabled', 1))
	{
		return true;
	}


	$headdata = $document->getHeadData();
	if ($headdata != null)
	{
		$keys= array_keys( $headdata['scripts']);
		$hs_base    = JURI::root(true). '/plugins/content/highslide/config/js/';
		foreach ($keys as $script )
		{
			$script = str_ireplace( $hs_base, "", $script );
			if ($script == "highslide-sitesettings.js"
				||JString::strpos( $script, "highslide-article-") === 0
				)
			{
				//	already have a config, get out
				return;
			}
		}
	}

	$needHighslide	=	($params->get('includehsconfig', 1) == 1);

	if (! $needHighslide)
	{
		$buffers = $document->GetBuffer();
		foreach( $buffers as $bufarray )
		{
			$buf = implode( '', $bufarray );
			if (preg_match( "/(class|rel)=\".*highslide/i", $buf, $match ))
			{
				$needHighslide = TRUE;
				break;
			}

		}
	}

	if ($needHighslide)
	{
		$dir = dirname( __FILE__ );
		$pos = JString::strrpos( $dir, "system" );
		if ($pos !== false)
		{
			$dir = JString::substr_replace( $dir, 'content', $pos );
		}
		require_once( $dir.DS.'highslide.php' );
		plgContentHighslide::_checkContent( $params, -1 );
	}
	return true;
}

?>