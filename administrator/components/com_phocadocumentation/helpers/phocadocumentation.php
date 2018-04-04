<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
jimport('joomla.application.component.controller');


class PhocaDocumentationHelper
{
	/**
	 * Method to get Phoca Version
	 * @return string Version of Phoca Gallery
	 */
	function getPhocaVersion()
	{
		$folder = JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_phocadocumentation';
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DS. 'components'.DS.'com_phocadocumentation';
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DS.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}
	
		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}
	
	function getPhocaId($id){
		$v	= PhocaDocumentationHelper::getPhocaVersion();
		$i	= str_replace('.', '',substr($v, 0, 3));
		$n	= '<p>&nbsp;</p>';
		$l	= 'h'.'t'.'t'.'p'.':'.'/'.'/'.'w'.'w'.'w'.'.'.'p'.'h'.'o'.'c'.'a'.'.'.'c'.'z'.'/'.'p'.'h'.'o'.'c'.'a'.'d'.'o'.'c'.'u'.'m'.'e'.'n'.'t'.'a'.'t'.'i'.'o'.'n';
		$t	= 'P'.'o'.'w'.'e'.'r'.'e'.'d'.' '.'b'.'y';
		$p	= 'P'.'h'.'o'.'c'.'a'.' '.'D'.'o'.'c'.'u'.'m'.'e'.'n'.'t'.'a'.'t'.'i'.'o'.'n';
		$s	= 's'.'t'.'y'.'l'.'e'.'='.'"'.'t'.'e'.'x'.'t'.'-'.'d'.'e'.'c'.'o'.'r'.'a'.'t'.'i'.'o'.'n'.':'.'n'.'o'.'n'.'e'.'"';
		$s2	= 's'.'t'.'y'.'l'.'e'.'='.'"'.'t'.'e'.'x'.'t'.'-'.'a'.'l'.'i'.'g'.'n'.':'.'c'.'e'.'n'.'t'.'e'.'r'.';'.'c'.'o'.'l'.'o'.'r'.':'.'#'.'d'.'3'.'d'.'3'.'d'.'3'.'"';
		$b	= 't'.'a'.'r'.'g'.'e'.'t'.'='.'"'.'_'.'b'.'l'.'a'.'n'.'k'.'"';
		$i	= (int)$i * 3;
		$output	= '';
		if ($id != $i) {
			$output		.= $n;
			$output		.= '<div '.$s2.'>';
		}
		
		if ($id == $i) {
			$output	.= '<!-- <a href="'.$l.'">site: www.phoca.cz | version: '.$v.'</a> -->';
		} else {
			$output	.= $t . ' <a href="'.$l.'" '.$s.' '.$b.' title="'.$p.'">'. $p. '</a>';
		}
		if ($id != $i) {
			$output		.= '</div>' . $n;
		}
		return $output;
	}
}
?>