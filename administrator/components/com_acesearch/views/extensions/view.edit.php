<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

// Imports
AcesearchUtility::import('library.elements.fieldlist');
AcesearchUtility::import('library.elements.handlerlist');
AcesearchUtility::import('library.elements.multiselectsql');

class AcesearchViewExtensions extends AcesearchView {
	
	function edit($tpl = null){
        $row = $this->getModel()->getEditData('AcesearchExtensions');
		
		$row->params = self::_getParams($row->extension, $row->params);
		
		// Get description from XML
		$xml_file = JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.$row->extension.'.xml';
		if (file_exists($xml_file)) {
			$row->description = AcesearchUtility::getXmlText($xml_file, 'description');
		}
		
		// Get behaviors
		JHTML::_('behavior.combobox');
		JHTML::_('behavior.tooltip');
		
		// Import pane
		jimport('joomla.html.pane');
		$tabs =& JPane::getInstance('Tabs');
		
		// Get search values              
		$this->assignRef('row',		$row);
		$this->assignRef('tabs', 	$tabs);
		
		parent::display($tpl);
	}
	
	function _getParams($option, $data) {
		$params = new JParameter($data);
		
		// Get extension's parameters
		$xml = & self::_getParamsXML($option);
		if (is_a($xml, 'JSimpleXMLElement')) {
			$params->setXML($xml);
		}
		elseif (is_array($xml) && count($xml) > 0) {
			for ($i = 0, $n = count($xml); $i < $n; $i++) {
				if (is_a($xml[$i], 'JSimpleXMLElement')) {
					$params->setXML($xml[$i]);
				}
			}
		}
		
		// Get default parameters
		$files = array('params');
		foreach ($files as $file) {
			$xml = & self::_getParamsXML("", $file);
			if (is_a($xml, 'JSimpleXMLElement')) {
				$params->setXML($xml);
			}
			elseif (is_array($xml) && count($xml) > 0) {
				for ($i = 0, $n = count($xml); $i < $n; $i++) {
					if (is_a($xml[$i], 'JSimpleXMLElement')) {
						$params->setXML($xml[$i]);
					}
				}
			}
		}

        return $params;
    }
    
    // Get extension's parameters
	function &_getParamsXML($option = "", $file = "") {
		static $xmls;

        if (! isset($xmls)) {
            $xmls = array();
        }
		
		$dest = $option;
		if (empty($option)) {
			$dest = $file;
		}

        if (!isset($xmls[$dest])) {
            $xmls[$dest] = null;
			
			if ($dest == $option) {
				$xml = & self::_getXML($dest);
			} elseif ($dest == $file) {
				$xml = & self::_getDefaultXML($dest);
			}

            if ($xml) {
                $document = & $xml->document;

                $xmls[$dest] = array();
                
                if (isset($document->params)) {
                    for ($i = 0, $n = count($document->params); $i < $n; $i++) {
                        if (isset($document->params[$i]->param)) {      
                            $xmls[$dest][] =& $document->params[$i];
                        }
                    }
                }
            }
        }

        return $xmls[$dest];
    }

    // Get the extension XML object
	function &_getXML($extension) {
        static $xmls;

        if (!isset($xmls)) {
            $xmls = array();
        }

        if (!isset($xmls[$extension])) {
            $xmls[$extension] = null;

            $xmlFile = JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.$extension.'.xml';
            if (JFile::exists($xmlFile)) {
                $xmls[$extension] = JFactory::getXMLParser('Simple');
                if (!$xmls[$extension]->loadFile($xmlFile)) {
                    $xmls[$extension] = null;
                }
            }
        }
		
        return $xmls[$extension];
    }

    // Get default XML object
	function &_getDefaultXML($file) {
        static $xmls;

        if (!isset($xmls)) {
            $xmls = array();
        }

        if (!isset($xmls[$file])) {
            $xmls[$file] = null;
			
            $xmlFile = JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.'default_'.$file.'.xml';;
            if (JFile::exists($xmlFile)) {
                $xmls[$file] = JFactory::getXMLParser('Simple');
                if (!$xmls[$file]->loadFile($xmlFile)) {
                    $xmls[$file] = null;
                }
            }
        }
		
        return $xmls[$file];
    }
}