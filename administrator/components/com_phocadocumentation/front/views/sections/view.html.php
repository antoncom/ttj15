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

class PhocaDocumentationViewSections extends JView
{

	function display($tpl = null)
	{		
		global $mainframe;
		
		$params 		= &$mainframe->getParams();
		$model			= &$this->getModel();
		$document		= &JFactory::getDocument();
		$section		= $model->getSectionList($params);
		$mostViewedDocs	= $model->getMostViewedDocsList($params);
		$tmpl			= array();
		
		$css			= 'phocadocumentation';
		$document->addStyleSheet(JURI::base(true).'/components/com_phocadocumentation/assets/'.$css.'.css');
		$document->addCustomTag("<!--[if lt IE 7]>\n<link rel=\"stylesheet\" href=\""
		.JURI::base(true)
		."/components/com_phocadocumentation/assets/".$css."-ie6.css\" type=\"text/css\" />\n<![endif]-->");
		$tmpl['id']		= PhocaDocumentationHelper::getPhocaId($params->get( 'display_id', 1 ));
		$tmpl['display_up_icon']		= $params->get( 'display_up_icon', 1 );
		$tmpl['article_itemid']			= $params->get( 'article_itemid', '' );
		$tmpl['displaynumdocsecs']		= $params->get( 'display_num_doc_secs', 0 );
		$tmpl['displaynumdocsecsheader']= $params->get( 'display_num_doc_secs_header', 1 );
		
		$this->assignRef('tmpl',				$tmpl);
		$this->assignRef('section',				$section);
		$this->assignRef('mostvieweddocs',		$mostViewedDocs);
		$this->assignRef('params',				$params);
		parent::display($tpl);
		
	}
}
?>