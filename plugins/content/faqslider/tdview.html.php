<?php
/*
* @name view.html.php
* @type Based on Joomla! view.html.php for FAQ Slider Plugin, a Joomla 1.5 Plugin
* @author Matt Faulds
* @website http://www.trafalgardesign.com
* @email webmaster@trafalgardesign.com
* @copyright Copyright (C) 2009 Trafalgar Design (Trafalgar Press (IOM) Ltd). All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
* FAQ Slider Plugin is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once (JPATH_BASE.DS.'components'.DS.'com_content'.DS.'view.php');
	
/**
 * HTML Article View class for the Content component
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class ContentViewTDArticle extends ContentView
{
	function display($tpl = null)
	{
		$mainframe  = &JFactory::getApplication();
		
		$user		= &JFactory::getUser();
		$document	= &JFactory::getDocument();
		$dispatcher	= &JDispatcher::getInstance();
		
		// Initialize variables
		$article	= &$this->get('Article');
		$params	    = &$article->parameters;

		$plugin     = &JPluginHelper::getPlugin('content', 'faqslider');
		$fsParams   = new JParameter( $plugin->params );
	  	
	  	$params->set('show_title', ($fsParams->get('show_title', '0') != '' ? $fsParams->get('show_title', '0') : $params->get('show_title')));
		$params->set('link_titles', ($fsParams->get('link_titles', '0') != '' ? $fsParams->get('link_titles', '0') : $params->get('link_titles')));
	  	$params->set('show_intro', ($fsParams->get('show_intro', '0') != '' ? $fsParams->get('show_intro', '0') : $params->get('show_intro')));
	  	$params->set('show_section', ($fsParams->get('show_section', '0') != '' ? $fsParams->get('show_section', '0') : $params->get('show_section')));
	  	$params->set('show_category', ($fsParams->get('show_category', '0') != '' ? $fsParams->get('show_category', '0') : $params->get('show_category')));
	  	$params->set('show_author', ($fsParams->get('show_author', '0') != '' ? $fsParams->get('show_author', '0') : $params->get('show_author')));
	  	$params->set('show_create_date', ($fsParams->get('show_create_date', '0') != '' ? $fsParams->get('show_create_date', '0') : $params->get('show_create_date')));
	  	$params->set('show_modify_date', ($fsParams->get('show_modify_date', '1') != '' ? $fsParams->get('show_modify_date', '1') : $params->get('show_modify_date')));
	  	//$params->set('show_item_navigation', ($fsParams->get('show_item_navigation', '0') != '' ? $fsParams->get('show_item_navigation', '0') : $params->get('show_item_navigation')));
	  	$params->set('show_vote', ($fsParams->get('show_vote', '0') != '' ? $fsParams->get('show_vote', '0') : $params->get('show_vote')));
	  	$params->set('show_icon', ($fsParams->get('show_icon', '0') != '' ? $fsParams->get('show_icon', '0') : $params->get('show_icon')));
	  	$params->set('show_pdf_icon', ($fsParams->get('show_pdf_icon', '0') != '' ? $fsParams->get('show_pdf_icon', '0') : $params->get('show_pdf_icon')));
	  	$params->set('show_print_icon', ($fsParams->get('show_print_icon', '0') != '' ? $fsParams->get('show_print_icon', '0') : $params->get('show_print_icon')));
	  	$params->set('show_email_icon', ($fsParams->get('show_email_icon', '0') != '' ? $fsParams->get('show_email_icon', '0') : $params->get('show_email_icon')));
	  	
		/*if($this->getLayout() == 'pagebreak') {
			$this->_displayPagebreak($tpl);
			return;
		}*/

		if (($article->id == 0))
		{
			$id = JRequest::getVar( 'id', '', 'default', 'int' );
			//return JError::raiseError( 404, JText::sprintf( 'Article # not found', $id ) );
			return;
		}
		
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// Create a user access object for the current user
		$access = new stdClass();
		$access->canEdit	= $user->authorize('com_content', 'edit', 'content', 'all');
		$access->canEditOwn	= $user->authorize('com_content', 'edit', 'content', 'own');
		$access->canPublish	= $user->authorize('com_content', 'publish', 'content', 'all');

		// Check to see if the user has access to view the full article
		$aid	= $user->get('aid');

		if ($article->access <= $aid) {
			$article->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug, $article->sectionid));;
		} else {
			return JText::_('ALERTNOTAUTH');
		}

		// Make valid breaks
		$article->text = str_replace(array('<br>','<br/>'), '<br />', $article->text);

		/*
		 * Process the prepare content plugins
		 */
		JPluginHelper::importPlugin('content');
		$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, $limitstart));
		
		// Set page title
		$params->set('page_title',	$article->title);
		
		/*
		 * Handle display events
		 */
		$article->event = new stdClass();
		$results = $dispatcher->trigger('onAfterDisplayTitle', array ($article, &$params, $limitstart));
		$article->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onBeforeDisplayContent', array (&$article, &$params, $limitstart));
		$article->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onAfterDisplayContent', array (&$article, &$params, $limitstart));
		$article->event->afterDisplayContent = trim(implode("\n", $results));
		
		// Show a read more link
		if($fsParams->get('show_readmore',0)) {
			if(strlen(strip_tags($article->text)) > $fsParams->get('readmore_limit',500)) {
				$article->text = substr($article->text, 0, $fsParams->get('readmore_limit',500));
				$article->text .= '<a href="'.$article->readmore_link.'" class="fsreadon">'.$fsParams->get('readmore_link','Read More...').'</a>';
			}
		}
	  	
		$this->assignRef('article', $article);
		$this->assignRef('params' , $params);
		$this->assignRef('user'   , $user);
		$this->assignRef('access' , $access);
		$this->assignRef('print', $print);

		return $this->loadTemplate($tpl);
	}

	function _displayPagebreak($tpl)
	{
		$document = &JFactory::getDocument();
		$document->setTitle(JText::_('PGB ARTICLE PAGEBRK'));

		parent::display($tpl);
	}
}