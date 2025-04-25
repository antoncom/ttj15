<?php
/*
* @name FAQ Slider Plugin 0.7.4
* @type Joomla 1.5 Plugin
* @author Matt Faulds
* @website http://www.trafalgardesign.com
* @email webmaster@trafalgardesign.com
* @copyright Copyright (C) 2009 Trafalgar Design (Trafalgar Press IOM Ltd.). All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
* FAQ Slider Plugin is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.plugin.plugin');

class plgContentFaqSlider extends JPlugin
{
	// global var containing article catid
	private $_vars = array('fsArticleCatid' => null);
	
	private $tabCount = 1;
	
	private $sliderCount = 1;
	
	public function plgContentFaqSlider( &$subject, $config )
	{
		parent::__construct( $subject, $config ); 									//Call the parent constructor
		$this->_plugin = &JPluginHelper::getPlugin( 'content', 'faqslider' ); 		//Get a reference to this plugin
		$this->params = new JParameter( $this->_plugin->params ); 					//Get the plugin parameters
	}
	
	public function onPrepareContent( &$article, &$params, $limitstart )
	{
		JPlugin::loadLanguage( 'plg_content_faqslider', JPATH_ADMINISTRATOR );		//Load the plugin language file - not in contructor in case plugin called by third party components
		$app = &JFactory::getApplication();
		
		if( $app->isAdmin() ) {
			return true;
		} else {
			if(!($this->_vars['fsArticleCatid'])) {
				$this->_vars['fsArticleCatid'] = $article->catid;
			}
							
			$regex = "#{faqslider\b(.*?)\}(.*?){/faqslider}#s";
			$article->text = preg_replace_callback( $regex, array('plgContentFaqSlider', 'faqslider_replacer'), $article->text, -1, $count );
			
			if($count) {
				if($this->params->get('css',1)) {
					$doc = &JFactory::getDocument();
					$doc->addStyleSheet(JURI::base().'plugins/content/faqslider/faqslider.css');
				}
			}
		}
		$this->_vars['fsArticleCatid'] = null;
	}
	
	private function faqslider_replacer ( &$matches )
	{
		if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
			jimport( 'joomla.error.profiler' );
			$p = JProfiler::getInstance('FAQ Slider');
			$p->mark('Start');
		}
		
		$app = &JFactory::getApplication();
		$user = &JFactory::getUser();
		$database = &JFactory::getDBO();
		
		$html = '<div id="faqslider">';
		$htmlArray = array();
		$option = 'N/A';
		$exp = 'N/A';
		$nested = 'N/A';
		$category = 'N/A';
		$query = 'N/A';
		
		$type = $this->params->get('defaulttype','sliders');
		$source = $this->params->get('defaultsource','art');
		
		$profileHtml = '';
		$fsErrorMsg = '';
		
		$fs_cmd = addslashes(strip_tags(trim($matches[1])));
		$fs_cmdPos = plgContentFaqSlider::padVar($fs_cmd);
		if(($pos = strpos($fs_cmd,"/")) != ($lpos = strrpos($fs_cmd,"/"))) {
			$source = substr($fs_cmd,0,$pos);
			$type = substr($fs_cmd,$pos+1,$lpos-$pos-1);
			$option = substr($fs_cmd,$lpos+1);
		} elseif($pos) {
			$source = substr($fs_cmd,0,$pos);
			$type = substr($fs_cmd,$pos+1);
			$option = $type;
		}
		
		if(strpos($matches[2],"==")) {
			$matches[2] = str_replace("==","",$matches[2]);
			$fs_match_container = "= '$matches[2]'";
		} else {
			$fs_match_container = "LIKE '%$matches[2]%'";
		}
		
		$source == 'section' ? $section = true : $section = false;
		
		if($option == 'N/A') {
			switch($fs_cmd)
			{
				case 'tabs':
					$type = 'tabs';
				break;
				
				case 'sliders':
					$type = 'sliders';
				break;
				
				case 'section':
					$section = true;
				break;
				
				case 'art':
					$source = 'art';
				break;
				
				case 'mod':
					$source = 'mod';
				break;
				
				case 'inline':
					$source = 'inline';
				break;
				
				case 'exp':
				case 'expand':
					$exp = 1;
				break;
				
				case 'nested':
					$nested = 1;
				break;
				
				case 'insert':
					$type = 'insert';
				break;
				
				default:
				break;
			}
		}
		
		switch($type)
		{
			case 'exp':
			case 'expand':
				$exp = 1;
				$type = $this->params->get('defaulttype','sliders');
			break;
			
			case 'nested':
				$nested = 1;
				$type = $this->params->get('defaulttype','sliders');
			break;
			
			case 'insert':
				$html = '';
			break;
			
			default:
			break;
		}
		
		switch($option)
		{
			case 'exp':
			case 'expand':
				$exp = 1;
			break;
			
			case 'nested':
				$nested = 1;
			break;
			
			default:
				$option = plgContentFaqSlider::padVar($option);
			break;
		}

		$aid = $user->get('aid', 0);
		
		if($section) {
			$section = addslashes(strip_tags(trim($matches[2])));
				        
			$query = "SELECT cat.*"
			 	. "\n FROM #__sections as sec, #__categories as cat"
			 	. "\n WHERE sec.title ". $fs_match_container
	            . "\n AND sec.access <= ". (int)$aid
	            . "\n AND sec.published = 1"
	            . "\n AND sec.id = cat.section"
	            . "\n AND cat.access <= ". (int)$aid
	            . "\n AND cat.published = 1"
	            . "\n ORDER BY cat.". $this->params->get('catordering','ordering');
	           
		    $database->setQuery( $query );
   		    if(!$categoryArray = $database->loadAssocList()) {
				if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
					$profileHtml = plgContentFaqSlider::getProfileHtml($p,'sectionFail');
				}
				$fsErrorMsg = JText::sprintf('FS_DEBUGGING_SECTION_FAIL',$section);
				$html = JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
				return $html;
   		    }

		    foreach($categoryArray as $key=>&$category) {
				if($this->_vars['fsArticleCatid'] == $category['id']) {
					unset($categoryArray[$key]);
				} else {
					if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
						$p->mark('catProcessSuccess');
					}					
					$query = "SELECT art.*"
				 	. "\n FROM #__categories as cat, #__content as art"
		            . "\n WHERE cat.id = ". (int)$category['id']
		            . "\n AND cat.access <= ". (int)$aid
	   	            . "\n AND cat.published = 1"
		            . "\n AND cat.id = art.catid"
		            . "\n AND art.access <= ". (int)$aid
		            . "\n AND art.state = 1"
		            . "\n ORDER BY art.". $this->params->get('ordering','ordering');
		            
				    $database->setQuery( $query );
				    $tdarticles = $database->loadObjectList();
			    	
			    	if($this->params->get('processart',1)) {
						//allow processing and display as article
						require_once('faqslider'.DS.'tdarticle.php');
						require_once('faqslider'.DS.'tdview.html.php');
					}
			    	
			    	foreach($tdarticles as &$tdarticle) {
						if($this->params->get('processart',1)) {
							$articleModel= new ContentModelArticle;
							$articleModel->setId($tdarticle->id);
							
							$articleView = new ContentViewTDArticle;
							$articleView->addTemplatePath(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl');
							$articleView->setModel($articleModel,"true");
													
							$htmlArray[$category['title']][$category['description']][$tdarticle->title] = $articleView->display();
							if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
								$p->mark('artProcessSuccess');
							}
						} else {
							$htmlArray[$category['title']][$category['description']][$tdarticle->title] = $tdarticle->introtext.' '.$tdarticle->fulltext;
							if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
								$p->mark('artTextSuccess');
							}
						}
			    	}
				}
		    }
		     
		    //instantiate tabs and sliders
	        require_once('plugins'.DS.'content'.DS.'faqslider'.DS.'tdpane.php');
	        ($tabOffset = JRequest::getInt('faqtab')) ? $tabOptions = array('startOffset' => $tabOffset) : $tabOptions = array();
			$tabPane = &TDPane::getInstance('tabs', $tabOptions);
			if($this->params->get('native',1)) {
				!($this->params->get('startclosed',-1)) ? $startTransition = 1 : $startTransition = 0;
				$options =  array('native'=>$this->params->get('native',1),'allowAllClose'=>'true','startOffset'=>$this->params->get('startclosed',-1),'startTransition'=>$startTransition,'opacityTransition'=>$this->params->get('opacity',1),'heightTransition'=>$this->params->get('height',1));
				$slidersPane = &TDPane::getInstance('sliders', $options);
			} else {
				$expandCollapse = 0;
				if($exp == 1 AND $this->params->get('expandcollapse',1)) {
					$expandCollapse = 1;
					//$expandCollapseLinks = JText::sprintf('FS_EXPANDCOLLAPSE_LINKS','|');
					$html .= JText::sprintf('FS_EXPANDCOLLAPSE_LINKS',$this->params->get('ecsep','|'))."<br />";
				}
				$options =  array('native'=>$this->params->get('native',1),'refocus'=>$this->params->get('refocus',1),'expandCollapse'=>$expandCollapse);
				$slidersPane = &TDPane::getInstance('sliders', $options);
			}
			
			$html = plgContentFaqSlider::buildTabsandSliders($html,$htmlArray,$tabPane,$slidersPane);
			
			if($this->params->get('debug',0)) {
				$category = 'N/A';
				$sectionTitleArray = array();
				$query = "SELECT sec.title"
			 	. "\n FROM #__sections as sec"
			 	. "\n WHERE sec.title ". $fs_match_container
	            . "\n AND sec.access <= ". (int)$aid
	            . "\n AND sec.published = 1";
	            $database->setQuery($query);
	            $sectionsArray = $database->loadAssocList();
	            foreach($sectionsArray as &$sectionArray) {
	            	foreach($sectionArray as &$sectionTitle) {
				    	$sectionTitleArray[] = $sectionTitle;
		            }
			    }
		    	$sections = implode($sectionTitleArray, ", ");
	   			if($this->params->get('developer',0)) {
					$profileHtml = plgContentFaqSlider::getProfileHtml($p,'End');
	   			}
				$fsErrorMsg = JText::sprintf('FS_DEBUGGING_SECTION_SUCCESS',$section,$sections);
				$html .= JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
			}
   		} else {
			if($source == 'mod') {					
				$category = addslashes(strip_tags(trim($matches[2])));
				
				$type == 'insert' ? $search = 'm.title' : $search = 'm.position';
				$query = "SELECT id, title, module, position, content, showtitle, control, params"
				. "\n FROM #__modules AS m"
				. "\n WHERE m.published = 1"
				. "\n AND m.access <= ". (int)$aid
				. "\n AND m.client_id = ". (int)$app->getClientId()
				. "\n AND $search LIKE '%$category%'"
				. "\n ORDER BY m.". $this->params->get('modordering','ordering');
				
				$database->setQuery($query);
				
				if(!$modules = $database->loadObjectList()) {
					if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
						$profileHtml = plgContentFaqSlider::getProfileHtml($p,'modFail');
					}
					$fsErrorMsg = JText::sprintf('FS_DEBUGGING_MODULE_FAIL',$category);
					$html = JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
					return $html;
				} else {
				
					// do some stuff that is found in libraries/joomla/application/module/helper.php
					$total = count($modules);
					for($i = 0; $i < $total; $i++)
					{
						//determine if this is a custom module
						$file					= $modules[$i]->module;
						$custom 				= substr( $file, 0, 4 ) == 'mod_' ?  0 : 1;
						$modules[$i]->user  	= $custom;
						// CHECK: custom module name is given by the title field, otherwise it's just 'om' ??
						$modules[$i]->name		= $custom ? $modules[$i]->title : substr( $file, 4 );
						$modules[$i]->style		= null;
						$modules[$i]->position	= strtolower($modules[$i]->position);
					}
	
					$document	= &JFactory::getDocument();
					$renderer	= $document->loadRenderer('module');
					$option ? $style = trim($option) : $style = trim($this->params->get('modstyle','table'));
					
					$moduleTitleArray = array();
					foreach ($modules as $mod)  {
						if($this->params->get('debug',0)) {
							$type == 'insert' ? $moduleTitleArray[] = $mod->title : $moduleTitleArray[] = $mod->position;
						}
						$attribs = array();
						$attribs['style'] = $style;
						$htmlArray[$mod->title] = $renderer->render($mod, $attribs);
					}
					if($this->params->get('debug',0)) {
						$moduleTitleArray = array_unique($moduleTitleArray);
						$this->_vars['moduleTitles'] = implode($moduleTitleArray, ", ");
						if($this->params->get('developer',0)) {
							$p->mark('modSuccess');
						}
					}
				}
			} elseif($source == 'art') {
				$category = addslashes(strip_tags(trim($matches[2])));
				
				if($type == 'insert') {
					$query = "SELECT art.*"
				 	. "\n FROM #__content as art"
		            . "\n WHERE art.title ". $fs_match_container
		            . "\n AND art.access <= ". (int)$aid
		            . "\n AND art.state = 1"
		            . "\n ORDER BY art.". $this->params->get('ordering','ordering');
				} else {
					$query = "SELECT art.*"
				 	. "\n FROM #__categories as cat, #__content as art"
		            . "\n WHERE cat.title ". $fs_match_container
		            . "\n AND cat.access <= ". (int)$aid
	   	            . "\n AND cat.published = 1"
		            . "\n AND cat.id = art.catid"
		            . "\n AND art.access <= ". (int)$aid
		            . "\n AND art.state = 1"
		            . "\n ORDER BY art.". $this->params->get('ordering','ordering');
				}
	            
			    $database->setQuery( $query );
			    $tdarticles = $database->loadObjectList();
			    
			    if($this->params->get('debug',0)) {
		    		$categoryTitleArray = array();
			    	if($type == 'insert') {
						$query = "SELECT art.title"
					 	. "\n FROM #__content as art"
			            . "\n WHERE art.title ". $fs_match_container
			            . "\n AND art.access <= ". (int)$aid
			            . "\n AND art.state = 1";
			    	} else {
						$query = "SELECT cat.title"
					 	. "\n FROM #__categories as cat"
			            . "\n WHERE cat.title ". $fs_match_container
			            . "\n AND cat.access <= ". (int)$aid
			            . "\n AND cat.published = 1";
			    	}
				    $database->setQuery( $query );
				    $categoriesArray = $database->loadAssocList();
		            foreach($categoriesArray as &$categoryArray) {
		            	foreach($categoryArray as &$categoryTitle) {
					    	$categoryTitleArray[] = $categoryTitle;
			            }
				    }
			    	$this->_vars['categories'] = implode($categoryTitleArray, ", ");
			    }
			    			
				if(!$tdarticles) {
					if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
						$profileHtml = plgContentFaqSlider::getProfileHtml($p,'artFail');
					}
					$type == 'insert' ? $fsErrorMsg = JText::sprintf('FS_DEBUGGING_ART_FAIL',$category) : $fsErrorMsg = JText::sprintf('FS_DEBUGGING_CAT_FAIL',$category);
					$html = JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
					return $html;
				} else {
					if($this->params->get('processart',1) AND !($type == 'sliders' AND $nested == 1) AND !($type == 'insert' AND !strpos($option,'process'))) {
						//allow processing and display as article
						require_once('faqslider'.DS.'tdarticle.php');
						require_once('faqslider'.DS.'tdview.html.php');
					}
					
					foreach($tdarticles as $tdarticle) {
						if($this->_vars['fsArticleCatid'] == $tdarticle->catid AND $this->params->get('processart',1) == 1) {
							if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
								$profileHtml = plgContentFaqSlider::getProfileHtml($p,'artFail');
							}
							isset($this->_vars['categories']) ? $fsErrorMsg = JText::sprintf('FS_DEBUGGING_ART_OWN_CAT_DEBUG',$category,$this->_vars['categories']) : $fsErrorMsg = JText::sprintf('FS_DEBUGGING_ART_OWN_CAT',$category);
							$html = JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
							return $html;
							break;
						}
						
						if($this->params->get('processart',1) AND !($type == 'sliders' AND $nested == 1) AND !($type == 'insert' AND !strpos($option,'process'))) {
							$articleModel= new ContentModelArticle;
							$articleModel->setId($tdarticle->id);
							
							$articleView = new ContentViewTDArticle;
							$articleView->addTemplatePath(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'views'.DS.'article'.DS.'tmpl');
							$articleView->setModel($articleModel,"true");
													
							$htmlArray[$tdarticle->title] = $articleView->display();
							if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
								$p->mark('artProcessSuccess');
							}
						} else {
							$htmlArray[$tdarticle->title] = $tdarticle->introtext.' '.$tdarticle->fulltext;
							if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
								$p->mark('artTextSuccess');
							}
						}
					}
				}
			} elseif($source == 'inline') {
				$text = trim($matches[2]);
				
				if(strpos(plgContentFaqSlider::padVar($text),"[[")) {
					$text = str_replace(array('[[',']]'),array('{','}'),$text);
				}
				
				// no tabs/sliders mode
				if($type == 'html') {
					$html = $text;
					if($this->params->get('debug',0)) {
						if($this->params->get('developer',0)) {
							$profileHtml = plgContentFaqSlider::getProfileHtml($p,'inlineHtml');
						}
						$html .= JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
					}
					return $html;
				}
				
				$regex = "#<tr>.*?<td>(.*?)<\/td>.*?<td>([\s\S]*?)<\/td>.*?<\/tr>#su";
				preg_match_all($regex,$text,$matchAll);
				
				/*if(strpos($matchAll[2],"table id=sliders")) {
					preg_match_all($regex,$matches[2],$matchAllNested);
				}*/
	
				if(!empty($matchAll[1]) AND !empty($matchAll[2])) {
					$htmlArray = array_combine($matchAll[1],$matchAll[2]);
				} else {
					if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
						$profileHtml = plgContentFaqSlider::getProfileHtml($p,'inlineFail');
					}
					$fsErrorMsg = JText::sprintf('FS_DEBUGGING_INLINE_FAIL');
					$html = JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
					return $html;
				}
				//if nested sliders
				//$html = plgContentFaqSlider::buildTabsandSliders($html,$htmlArray,$tabPane,$slidersPane);
			} else {
				if($this->params->get('debug',0) AND $this->params->get('developer',0)) {
					$profileHtml = plgContentFaqSlider::getProfileHtml($p,'sourceFail');
				}
				$fsErrorMsg = JText::sprintf('FS_DEBUGGING_ERROR');
				$html = JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
				return $html;
			}
			
			if(!empty($htmlArray)) {
				require_once('plugins'.DS.'content'.DS.'faqslider'.DS.'tdpane.php');
					
				switch($type)
				{
					case 'tabs':
						($tabOffset = JRequest::getInt('faqtab')) ? $tabOptions = array('startOffset' => $tabOffset) : $tabOptions = array();
						$pane = &TDPane::getInstance('tabs', $tabOptions);
						$html .= $pane->startPane( 'faqTabs' );
					break;
					
					case 'sliders':
						if($this->params->get('native',1)) {
							!($this->params->get('startclosed',-1)) ? $startTransition = 1 : $startTransition = 0;
							$options =  array('native'=>$this->params->get('native',1),'allowAllClose'=>'true','startOffset'=>$this->params->get('startclosed',-1),'startTransition'=>$startTransition,'opacityTransition'=>$this->params->get('opacity',1),'heightTransition'=>$this->params->get('height',1));
							$pane = &TDPane::getInstance('sliders', $options);
						} else {
							$expandCollapse = 0;
							if($exp == 1 AND $this->params->get('expandcollapse',1)) {
								$expandCollapse = 1;
								$html .= JText::sprintf('FS_EXPANDCOLLAPSE_LINKS',$this->params->get('ecsep','|'));
							}
							$options =  array('native'=>$this->params->get('native',1),'refocus'=>$this->params->get('refocus',1),'expandCollapse'=>$expandCollapse);
							$pane = &TDPane::getInstance('sliders', $options);
						}
						$html .= $pane->startPane( 'faqSliders' );
					break;
					
					case 'insert':
						foreach($htmlArray as $key => $value) {
							strpos($option,'title') ? $html .= "<h3>$key</h3>" : $html .= "";
							$html .= "<div>$value</div>";
							//$html .= "<br />";
						}
						if($this->params->get('debug',0)) {
							if($this->params->get('developer',0)) {
								$profileHtml = plgContentFaqSlider::getProfileHtml($p,'End');
							}
							isset($this->_vars['categories']) ? $fsErrorMsg = JText::sprintf('FS_DEBUGGING_ART_SUCCESS',$category,$this->_vars['categories']) : $fsErrorMsg = '';
							$html .= JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
						}
						return $html;
					break;
				}
								
				$i = 1;
				foreach($htmlArray as $key => $value) {
					$html .= $pane->startPanel( $key, "panel$i" );
					$html .= $value;
					$html .= $pane->endPanel();
					$i++;
				}
				
				$html .= $pane->endPane();
			}
					
			if($this->params->get('debug',0)) {
				if($this->params->get('developer',0)) {
					$profileHtml = plgContentFaqSlider::getProfileHtml($p,'End');
				}
				if(isset($this->_vars['categories'])) {
					$fsErrorMsg = JText::sprintf('FS_DEBUGGING_CAT_SUCCESS',$category,$this->_vars['categories']);
				} elseif(isset($this->_vars['moduleTitles'])) {
					$fsErrorMsg = JText::sprintf('FS_DEBUGGING_MODULE_SUCCESS',$category,$this->_vars['moduleTitles']);
				} else {
					$fsErrorMsg = '';
				}
				$html .= JText::sprintf('FS_DEBUGGING_TEXT',$fsErrorMsg,$aid,$source,$type,$option,$category,$exp,$nested,$profileHtml);
			}
		}
		$html .= '</div>';
		return $html;
	}
    
    private function padVar($var)
	{
		if($var) {
			$varLength = strlen($var)+1;
			$var = str_pad($var,$varLength,' ',STR_PAD_LEFT);
		}
		
		return $var;
	}
	
	private function getProfileHtml(&$p, $mark)
	{
		$p->mark($mark);
		$profiles = $p->getBuffer();
		unset($p->_buffer);
		$profileHtml = '';
		foreach($profiles as $profile) {
			$profileHtml .= $profile."<br />";
		}
		return $profileHtml;
	}
	
	private function buildTabsandSliders($html,$htmlArray,$tabPane,$slidersPane)
	{
		//build tabs and sliders
		$html .= $tabPane->startPane( 'faqTabs' );
		$i = $this->tabCount;
		$j = $this->sliderCount;
		foreach($htmlArray as $catTitle => &$catData) {
			$html .= $tabPane->startPanel( $catTitle, "tabPanel$i" );
			foreach($catData as $catDesc => &$artData) {
				$this->params->get('catdesc',1) ? $html .= $catDesc : $html .= "<br />";
				//$this->params->get('expandcollapse',1) ? $html .= $expandCollapseLinks : $html .= '';
				$html .= $slidersPane->startPane( "faqSliders$i" );
				foreach($artData as $artTitle => $artText) {
					$html .= $slidersPane->startPanel( $artTitle, "sliderPanel$j" );
					$html .= $artText;
					$html .= $slidersPane->endPanel();
					$j++;
				}
			}
			$html .= $slidersPane->endPane();
			$html .= "<br />";
			$html .= $tabPane->endPanel();
			$i++;
		}
		$html .= $tabPane->endPane();
		$this->tabCount = $i;
		$this->sliderCount = $j;
		
		return $html;
	}
}