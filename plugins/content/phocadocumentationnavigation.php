<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @plugin Phoca Plugin
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * {phocadocumentation view=navigation|type=mpcn}
 * {phocadocumentation view=navigation|type=ptn|top=site}
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
if (!JComponentHelper::isEnabled('com_phocadocumentation', true)) {
	return JError::raiseError(JText::_('Phoca Download Error'), JText::_('Phoca Documentation is not installed on your system'));
}
require_once( JPATH_ROOT.DS.'components'.DS.'com_phocadocumentation'.DS.'helpers'.DS.'route.php' );

class plgContentPhocaDocumentationNavigation extends JPlugin
{	
	
	function plgContentPhocaDocumentationNavigation( &$subject, $params ) {
            parent::__construct( $subject, $params  );

    }

	function onPrepareContent( &$article, &$params, $limitstart = null ) {
		$user 		= &JFactory::getUser();
		$aid 		= $user->get('aid', 0);
		$db 		= &JFactory::getDBO();
		$document	= &JFactory::getDocument();
		JPlugin::loadLanguage( 'plg_content_phocadocumentationnavigation' );
		
		$component 		= 'com_phocadocumentation';
		$table 			=& JTable::getInstance('component');
		$table->loadByOption( $component );
		$paramsC	 	= new JParameter( $table->params );
		
		$tmpl['article_itemid']		= $paramsC->get( 'article_itemid', '' );
		
		$css			= 'phocadocumentation';
		$document->addStyleSheet(JURI::base(true).'/components/com_phocadocumentation/assets/'.$css.'.css');
		$document->addCustomTag("<!--[if lt IE 7]>\n<link rel=\"stylesheet\" href=\""
		.JURI::base(true)
		."/components/com_phocadocumentation/assets/".$css."-ie6.css\" type=\"text/css\" />\n<![endif]-->");

			
		// Start Plugin
		$regex_one		= '/({phocadocumentation\s*)(.*?)(})/si';
		$regex_all		= '/{phocadocumentation\s*.*?}/si';
		$matches 		= array();
		$count_matches	= preg_match_all($regex_all,$article->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
		$customCSS		= '';
		$customCSS2		= '';
		
	
		
		for($i = 0; $i < $count_matches; $i++) {
			
			// Plugin variables
			$view 		= '';
			$type		= '';
			$topid		= '';
			
			// Get plugin parameters
			$phocadocumentation	= $matches[0][$i][0];
			preg_match($regex_one,$phocadocumentation,$phocadocumentation_parts);
			$parts			= explode("|", $phocadocumentation_parts[2]);
			$values_replace = array ("/^'/", "/'$/", "/^&#39;/", "/&#39;$/", "/<br \/>/");

			foreach($parts as $key => $value) {
				$values = explode("=", $value, 2);
				
				foreach ($values_replace as $key2 => $values2) {
					$values = preg_replace($values2, '', $values);
				}
				
				// Get plugin parameters from article
				if($values[0]=='view') 	{ $view = $values[1];}
				if($values[0]=='type') 	{ $type = $values[1];}
				if($values[0]=='top') 	{ $topid = $values[1];}
				
			}
			
			$document->addCustomTag('<script type="text/javascript" src="'.JURI::root().'includes/js/overlib_mini.js"></script>');
			
			// PARAMS
			$fgColor	= '#fafafa';
			$bgColor	= '#fafafa';
			$textColor	= '#000000';
			$capColor	= '#000000';
			$closeColor	= '#000000';
			
			$output  = '';
			$output .= '<div class="phocadocumentation-navigation">' . "\n";
			
			
			// -------------------------
			// NAVIGATION
			// -------------------------
			if ($view == 'navigation') {
				
				$nextDoc 	= array();
				$nextPrev 	= array();
				$docList 	= array();
				$id			= JRequest::getVar('id', 0, '', 'int');
				
				// CURRENT DOC (Information about current doc - ordering, category)
				$wheres		= array();
				$wheres[]	= " c.id= ".(int)$id;
				$wheres[]	= " c.catid= cc.id";
				if ($aid !== null) {
					$wheres[] = "c.access <= " . (int) $aid;
					$wheres[] = "cc.access <= " . (int) $aid;
				}
				$wheres[] = " c.state = 1";
				$wheres[] = " cc.published = 1";
				
				$query 	= " SELECT c.id, c.title, c.alias, c.ordering, c.catid, cc.section, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias "
						 ." FROM #__content AS c, #__categories AS cc"
						 ." WHERE " . implode( " AND ", $wheres );
				
				$db->setQuery($query, 0, 1);
				$currentDoc = $db->loadObject();
							
				// DOC LIST (Table of contents), PREVIOUS, NEXT
				if (isset($currentDoc->ordering) && isset($currentDoc->catid)) {
					
					$wheres		= array();
					$wheres[]	= " c.catid= ".(int)$currentDoc->catid;
					$wheres[]	= " c.catid= cc.id";
					if ($aid !== null) {
						$wheres[] = "c.access <= " . (int) $aid;
						$wheres[] = "cc.access <= " . (int) $aid;
					}
					$wheres[] = " c.state = 1";
					$wheres[] = " cc.published = 1";
					
					$wheresNext = " AND c.ordering >".(int)$currentDoc->ordering;
					$wheresPrev = " AND c.ordering <".(int)$currentDoc->ordering;
					
					// Next arrow
					$queryNext = " SELECT c.id, c.title, c.alias, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias "
							." FROM #__content AS c, #__categories AS cc"
							. " WHERE " . implode( " AND ", $wheres )
							. $wheresNext
							. " ORDER BY c.ordering ASC";
					
					// Prev arrow
					$queryPrev = " SELECT c.id, c.title, c.alias, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias "
							." FROM #__content AS c, #__categories AS cc"
							. " WHERE " . implode( " AND ", $wheres )
							. $wheresPrev
							. " ORDER BY c.ordering DESC";
					
					// Table of contents
					$queryDocList = " SELECT c.id, c.title, c.alias, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias "
							." FROM #__content AS c, #__categories AS cc"
							. " WHERE " . implode( " AND ", $wheres )
							. " ORDER BY c.ordering";
				
					
					$db->setQuery($queryNext, 0, 1);
					$nextDoc = $db->loadObject();
					
					$db->setQuery($queryPrev, 0, 1);
					$prevDoc = $db->loadObject();
					
					$db->setQuery($queryDocList);
					$docList = $db->loadObjectList();
				}
				
				// PREVIOUS OUTPUT
				$prevOutput = '';
				if (!empty($prevDoc)) {
					$img	 = JHTML::_('image', 'components/com_phocadocumentation/assets/images/prev.png', JText::_('Previous'));
					$overlib = " onmouseover=\"return overlib('".$prevDoc->title."', CAPTION, '".JText::_('Previous')."', BELOW, RIGHT, BGCLASS, 'bgPhocaPDocClass', CLOSECOLOR, '".$closeColor."', FGCOLOR, '".$fgColor."', BGCOLOR, '".$bgColor."', TEXTCOLOR, '".$textColor."', CAPCOLOR, '".$capColor."');\"";
					$overlib .= " onmouseout=\"return nd();\"";
					
					$link = PhocaDocumentationHelperRoute::getArticleRoute($prevDoc->id, $prevDoc->alias, $prevDoc->categoryid, $prevDoc->categoryalias, $tmpl['article_itemid']);
					
					$prevOutput .= '<a '.$overlib.' href="'. JRoute::_($link).'">'.$img.'</a>'; // title="'.$prevDoc->title.'"
				} else {
					$img	= JHTML::_('image', 'components/com_phocadocumentation/assets/images/prev-grey.png', JText::_('Previous'));
					$prevOutput .= $img;
				
				}
				
				
				// DOC LIST (TABLE OF CONTENTS) OUTPUT
				$docListOutput = '';
				
				// DocListOutputBox
				$docListOutputBox = '';
				if (!empty($docList)) {
					$docListOutputBox .= '<div style="text-align:left" id="phoca-doc-category-box-plugin">';
					foreach ($docList as $value) {
						$link = PhocaDocumentationHelperRoute::getArticleRoute($value->id, $value->alias, $value->categoryid, $value->categoryalias, $tmpl['article_itemid']);
						$docListOutputBox .= '<p class="pdoc-document"><a title="'.$value->title.'" href="'. JRoute::_($link).'">'.$value->title.'</a></p>';
					}
					$docListOutputBox .= '</div>';
				}
				
				if (!empty($currentDoc)) {
					$img	= JHTML::_('image', 'components/com_phocadocumentation/assets/images/icon-category.png', JText::_('Table of Contents'));
					
					$overlib = " onmouseover=\"return overlib('".htmlspecialchars( addslashes('<div class="pdoc-overlib">'.$docListOutputBox.'</div>') )."', CAPTION, '".JText::_('Table of Contents')."', BELOW, RIGHT, BGCLASS, 'bgPhocaPDocClass', CLOSECOLOR, '".$closeColor."', FGCOLOR, '".$fgColor."', BGCOLOR, '".$bgColor."', TEXTCOLOR, '".$textColor."', CAPCOLOR, '".$capColor."', STICKY, MOUSEOFF, WIDTH, 400);\"";
					$overlib .= " onmouseout=\"return nd();\"";
					
					$docListOutput .= '<a '.$overlib.' href="'. JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($currentDoc->categoryid, $currentDoc->categoryalias, (int)$currentDoc->section)).'">'.$img.'</a>';
				}
				
			
				// NEXT OUTPUT
				$nextOutput = '';
				if (!empty($nextDoc)) {
					$img	= JHTML::_('image', 'components/com_phocadocumentation/assets/images/next.png', JText::_('Next'));
					
					$overlib = "onmouseover=\"return overlib('".$nextDoc->title."', CAPTION, '".JText::_('Next')."', BELOW, RIGHT, BGCLASS, 'bgPhocaPDocClass', CLOSECOLOR, '".$closeColor."', FGCOLOR, '".$fgColor."', BGCOLOR, '".$bgColor."', TEXTCOLOR, '".$textColor."', CAPCOLOR, '".$capColor."');\"";
					$overlib .= " onmouseout=\"return nd();\"";
					
					$link = PhocaDocumentationHelperRoute::getArticleRoute($nextDoc->id, $nextDoc->alias, $nextDoc->categoryid, $nextDoc->categoryalias, $tmpl['article_itemid']);
					
					$nextOutput .= '<a '.$overlib.' href="'. JRoute::_($link).'">'.$img.'</a>';
				} else {
					$img	= JHTML::_('image', 'components/com_phocadocumentation/assets/images/next-grey.png', JText::_('Next'));
					$nextOutput .= $img;
				
				}
				
				// TOP OUTPUT
				$topOutput = '';
				
				// add other top 
				if ($topid == '') {
					$topid = 'pdoc-top';// go to main navigation instead of top site
				}
				if (!empty($currentDoc)) {
					$img	= JHTML::_('image', 'components/com_phocadocumentation/assets/images/up.png', JText::_('Top'));
					$overlib = "onmouseover=\"return overlib('".$currentDoc->title."', CAPTION, '".JText::_('Top')."', BELOW, RIGHT, BGCLASS,'bgPhocaPDocClass', CLOSECOLOR, '".$closeColor."', FGCOLOR, '".$fgColor."', BGCOLOR, '".$bgColor."', TEXTCOLOR, '".$textColor."', CAPCOLOR, '".$capColor."');\"";
					$overlib .= " onmouseout=\"return nd();\"";
					
					$link = PhocaDocumentationHelperRoute::getArticleRoute($currentDoc->id, $currentDoc->alias, $currentDoc->categoryid, $currentDoc->categoryalias, $tmpl['article_itemid']) .'#'.$topid;
					
					$topOutput .= '<a '.$overlib.' href="'. JRoute::_($link).'">'.$img.'</a>';
				}
					
					
			    // MAIN OUTPUT
				
				$main 		= false;
				$prev 		= false;
				$next 		= false;
				$top 		= false;
				$content 	= false;
				$main 		= preg_match("/m/i", $type);
				$prev 		= preg_match("/p/i", $type);
				$next 		= preg_match("/n/i", $type);
				$top 		= preg_match("/t/i", $type);
				$content	= preg_match("/c/i", $type);
				$sep		= ' <b style="color:#ccc;">&bull;</b> ';
				$sepPrev	= 0;
				
				if ($main) {
					$output .= '<div class="navigation-text" id="pdoc-top"><div><div><div><h5>'.JText::_('Navigation') . '</h5>'."\n";
					$output .= '<div>';
				} else {
					$output .= '<div class="navigation-text" ><div><div><div>'."\n";
					$output .= '<div>';
				}
				if ($prev) {
					$output .= $prevOutput;
					$sepPrev = 1;
				}
				if ($content) {
					if ($sepPrev == 1) {
						$output .= $sep;
					}
					$output .= $docListOutput;
					$sepPrev = 1;
				}
				if ($top) {
					if ($sepPrev == 1) {
						$output .= $sep;
					}
					$output .= $topOutput;
					$sepPrev = 1;
				}
				if ($next) {
					if ($sepPrev == 1) {
						$output .= $sep;
					}
					$output .= $nextOutput;
					$sepPrev = 1;
				}
				
				
				
			}
				
			$output .= '</div></div></div></div></div></div>';

			$article->text = preg_replace($regex_all, $output, $article->text, 1);		
		}
		return true;
	}
}
?>