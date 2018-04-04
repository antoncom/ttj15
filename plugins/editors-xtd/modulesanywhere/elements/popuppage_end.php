<?php
/**
 * Element include: Popup Page (end)
 * Include file to make a selfcontained page that has all the administrator variables and style
 *
 * @package    NoNumber! Elements
 * @version    v1.0.3
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

// Create the new body contents

/* Get contents*/
$_new_html = ob_get_contents();
ob_end_clean();

/* RENDER THE APPLICATION */
$mainframe->render();


/* Place the html in the body */
$_JRESPONSE->body['0'] = str_replace( '</body>', $_new_html."\n</body>", $_JRESPONSE->body['0'] );

/* Remove system template templates */
$_JRESPONSE->body['0'] = str_replace( '/templates/system/css/template.css', '/templates/system/css/system.css', $_JRESPONSE->body['0'] );

/* Correct reference to admin templates */
$_JRESPONSE->body['0'] = str_replace( 'href="templates', 'href="'.dirname( $_SERVER['PHP_SELF'] ).'/templates', $_JRESPONSE->body['0'] );

// More adin stuff
/* RETURN THE RESPONSE */
echo JResponse::toString( $mainframe->getCfg( 'gzip' ) );