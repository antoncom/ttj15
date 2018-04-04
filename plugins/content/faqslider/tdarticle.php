<?php
/*
* @name tdarticle.php
* @type Based on Joomla! com_content
* @author Matt Faulds
* @website http://www.trafalgardesign.com
* @email webmaster@trafalgardesign.com
* @copyright Copyright (C) 2009 Trafalgar Design (Trafalagr Press (IOM) Ltd). All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JHTML::addIncludePath(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'helpers');
require_once(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'query.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');

jimport('joomla.application.component.helper');