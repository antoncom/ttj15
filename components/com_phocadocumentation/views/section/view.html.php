<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class PhocaDocumentationViewSection extends JView
{

	function display($tpl = null)
	{		
		global $mainframe;
		
		$params 		= &$mainframe->getParams();
		$model			= &$this->getModel();
		$document		= &JFactory::getDocument();
		$sectionId		= JRequest::getVar('id', 0, '', 'int');
		$section		= $model->getSection($sectionId, $params);
		$categoryList	= $model->getCategoryList($sectionId, $params);
		$mostViewedDocs	= $model->getMostViewedDocsList($sectionId, $params);
		$tmpl			= array();
		
		$css			= 'phocadocumentation';
		$document->addStyleSheet(JURI::base(true).'/components/com_phocadocumentation/assets/'.$css.'.css');
		$document->addCustomTag("<!--[if lt IE 7]>\n<link rel=\"stylesheet\" href=\""
		.JURI::base(true)
		."/components/com_phocadocumentation/assets/".$css."-ie6.css\" type=\"text/css\" />\n<![endif]-->");
		$tmpl['id']					= PhocaDocumentationHelper::getPhocaId($params->get( 'display_id', 1 ));
		$tmpl['display_up_icon']	= $params->get( 'display_up_icon', 1 );
		$tmpl['article_itemid']		= $params->get( 'article_itemid', '' );
		
		// Define image tag attributes
	      if (!empty($section[0]->image)) {
	         $attribs['align'] = '"'.$section[0]->image_position.'"';
	         $attribs['hspace'] = '"6"';

	         // Use the static HTML library to build the image tag
	         $tmpl['image'] = JHTML::_('image', 'images/stories/'.$section[0]->image, JText::_('Phoca Download'), $attribs);
	      } else {
	         $tmpl['image'] = '';
	      }
		  
		  
		// Breadcrumbs
		if (!empty($section[0]->title)) {
			$pathway 		=& $mainframe->getPathway();
			$pathway->addItem($section[0]->title, JRoute::_(PhocaDocumentationHelperRoute::getSectionsRoute()));
		}
		
		$this->assignRef('tmpl',         	$tmpl);
		$this->assignRef('section',			$section);
		$this->assignRef('categorylist',	$categoryList);
		$this->assignRef('mostvieweddocs',	$mostViewedDocs);
		$this->assignRef('params',			$params);
		parent::display($tpl);
		
	}
}
?>