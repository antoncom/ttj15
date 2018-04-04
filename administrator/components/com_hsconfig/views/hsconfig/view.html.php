<?php
/**
 * Highslide Configuration View for Highslide Configuration Component
 *
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * HsConfig View
 */
class HsConfigsViewHsConfig extends JView
{
	/**
	 * display method of HsConfig view
	 * @return void
	 **/
	function display($tpl = null)
	{
		// Load tooltips behavior
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');
		JHTML::_('stylesheet', 'icons.css', 'administrator/components/com_hsconfig/css/');

		//get the hsconfig
		$hsconfig	=& $this->get('Data');
		$isNew		= ($hsconfig->id == 0);
		$params		= new JParameter($hsconfig->params, JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_hsconfig' . DS . 'hsconfig.xml');

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Highslide JS Configuration' ).': <small><small>[ ' . $text.' ]</small></small>', 'config.png' );
		JToolBarHelper::custom('pubsave','savepublish.png', 'savepublish_f2.png', 'Save/Publish', false, true);
		JToolBarHelper::custom('pubapply','applypublish.png', 'applypublish_f2.png', 'Apply/Publish', false, true);
		JToolBarHelper::save();

		if ($isNew)
		{
			JToolBarHelper::cancel();
		}
		else
		{
			// for existing items changes can be applied
			JToolBarHelper::apply();
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'close', 'Close' );
		}

		$this->assignRef('hsconfig', $hsconfig);
		$this->assignRef('params', $params);
		if ($isNew)
		{
			$articlelist = $this->get('ArticleList');
			$this->assignRef('articlelist', $articlelist);
		}
		parent::display($tpl);
	}

	function doFiller( $count )
	{
		if ($count > 0)
		{
			echo "<div>";
			for ($i = 0; $i < ($count+8); $i++ )
			{
				echo "<br/>";
			}
			echo "</div>\n";
		}

	}

	function renderOverlayExampleInfos()
	{
		$maxcount = 0;

		echo HsConfigsViewHsConfig::PresetInfo( 'op-none-selected', $maxcount );
		echo HsConfigsViewHsCOnfig::PresetInfo( 'op-no-info', $maxcount );

		$path		= JPATH_ADMINISTRATOR.DS."components".DS."com_hsconfig".DS."presets".DS."overlay";
		$filter		= ".html";
		$files		= JFolder::files($path, $filter);

		$options = "";

		if ( is_array($files) )
		{
			foreach ($files as $file)
			{
				$file = JFile::stripExt( $file );
				if ($file != 'index')
				{
					echo HSConfigsViewHsConfig::PresetInfo( "op-".$file, $maxcount );
				}
			}
		}
		HSConfigsViewHsConfig::doFiller( $maxcount );
	}

	function renderScreenshotInfos()
	{
		$maxcount = 0;

		echo HsConfigsViewHsConfig::PresetInfo( 'ss-none-selected', $maxcount );
		echo HsConfigsViewHsCOnfig::PresetInfo( 'ss-no-info', $maxcount );

		$path		= JPATH_ADMINISTRATOR.DS."components".DS."com_hsconfig".DS."presets".DS."slideshow";
		$filter		= ".js";
		$files		= JFolder::files($path, $filter);

		$options = "";

		if ( is_array($files) )
		{
			foreach ($files as $file)
			{
				$file = JFile::stripExt( $file );
				echo HSConfigsViewHsConfig::PresetInfo( "ss-".$file, $maxcount );
			}
		}
		HSConfigsViewHsConfig::doFiller( $maxcount );
	}

	function PresetInfo( $name, &$maxcount )
	{
		$text = JText::_($name);
		if ($text == $name)
		{
			return "";
		}
		$info  = "<div id=\"hsconfig-".$name."\" style=\"display: none;width: 40%;position: absolute\">\n";
		$info .= "<br/><h4>".$text."</h4>\n";
		$text = JText::_($name." DESC");
		if ($text != $name." DESC")
		{
			$info .= "<p>".$text."</p>\n";
		}
		$text = JText::_($name." OVERRIDE0");
		$i = 0;
		$oinfo = "";
		while ($text != $name." OVERRIDE".$i)
		{
			if ($oinfo == "")
			{
				$oinfo = "<ul>\n";
			}
			$oinfo .= "<li>".$text."</li>\n";
			$i++;
			$text = JText::_($name." OVERRIDE".$i);
		}
		if ($oinfo != "")
		{
			$oinfo .= "</ul>\n";
		}
		$text = JText::_($name." NOTES0");
		$i = 0;
		$ninfo = "";
		while ($text != $name." NOTES".$i)
		{
			if ($ninfo == "")
			{
				$ninfo = "<p>\n";
			}
			$ninfo .= $text."\n";
			$i++;
			$text = JText::_($name." NOTES".$i);
		}
		if ($ninfo != "")
		{
			$ninfo .= "</p>\n";
		}
		$info .= $oinfo.$ninfo."</div>\n";
		if (isset($maxcount))
		{
			$lines = preg_match_all( "/\n/", $info, $arr );
			$maxcount = max( $maxcount, $lines );
		}
		return $info;
	}

	function WarningIcon()
	{
		global $mainframe;

		$tip = '<img src="'.JURI::root().'includes/js/ThemeOffice/warning.png" border="0"  alt="" />';

		return $tip;
	}
}