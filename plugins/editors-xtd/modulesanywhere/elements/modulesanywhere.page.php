<?php
/**
 * Modules Anywhere popup page
 * Selfcontained page that displays a list with modules
 *
 * @package    Modules Anywhere
 * @version    1.1.4
 * @since      File available since Release v1.0.0
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl/modulesanywhere
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// This is a selfcontained page
define( '_JEXEC', 1 );

// Include first popup page
include( 'popuppage_start.php' );

// Content

// Include the syndicate functions only once
jimport( 'joomla.plugin.plugin' );

// Add scripts and styles
$_document = & JFactory::getDocument();
$_script = "
	function modulesanywhere_jInsertEditorText( id, modulepos ) {
		f = document.getElementById( 'adminForm' );
		var style = f.style.value.trim();
		if ( modulepos ) {
			str = '{modulepos '+id+'}';
		} else {
			str = '{module '+id;
			if ( style ) {
				str += '|'+style;
			}
			str += '}';
		}
		window.parent.jInsertEditorText( str, '".JRequest::getVar( 'name' )."' );
		window.parent.document.getElementById( 'sbox-window' ).close();
	}
";
$_document->addScriptDeclaration( $_script );
$_document->addScript( JURI::root(true).'/includes/js/joomla.javascript.js');
$_document->addStyleSheet( JURI::root( true ).'/plugins/editors-xtd/modulesanywhere/css/modulesanywhere_popup.css' );

renderHTML();

// Include second file to output page
include( 'popuppage_end.php' );


function getPluginParams( $type, $plugin, $xml = '' ) {
	$_db =& JFactory::getDBO();
	$_query = "
		SELECT params
		FROM #__plugins
		WHERE folder = '".$type."'
		AND element = '".$plugin."'
		LIMIT 1";
	$_db->setQuery( $_query );
	$_params = $_db->loadResult();
	$_params = new JParameter( $_params );
	if ( $xml && isset( $_params->_registry ) && isset( $_params->_registry['_default'] ) && isset( $_params->_registry['_default']['data'] ) ) {
		$_params->loadSetupFile( $xml );
		$_xml_params = $_params->renderToArray();
		foreach( $_params->_registry['_default']['data'] as $_key => $_val ) {
			if ( $_val == '' && isset( $_xml_params[$_key] ) && isset( $_xml_params[$_key]['4'] ) ) {
				$_params->_registry['_default']['data']->$_key =  $_xml_params[$_key]['4'];
			}
		}
		foreach( $_xml_params as $_key => $_val ) {
			if ( $_key['0'] != '@' && !isset( $_params->_registry['_default']['data']->$_key ) && isset( $_val['4'] ) ) {
				$_params->_registry['_default']['data']->$_key =  $_val['4'];
			}
		}
	}

	return $_params;
}

function renderHTML()
{
	global $mainframe;

	// Initialize some variables
	$_db		=& JFactory::getDBO();
	$client	=& JApplicationHelper::getClientInfo( JRequest::getVar( 'client', '0', '', 'int' ) );
	$_option	= 'modulesanywhere';

	$_filter_order		= $mainframe->getUserStateFromRequest( $_option.'filter_order',		'filter_order',		'm.position',	'cmd' );
	$_filter_order_Dir	= $mainframe->getUserStateFromRequest( $_option.'filter_order_Dir',	'filter_order_Dir',	'',				'word' );
	$_filter_state		= $mainframe->getUserStateFromRequest( $_option.'filter_state',		'filter_state',		'',				'word' );
	$_filter_position	= $mainframe->getUserStateFromRequest( $_option.'filter_position',	'filter_position',	'',				'cmd' );
	$_filter_type		= $mainframe->getUserStateFromRequest( $_option.'filter_type',		'filter_type',		'',				'cmd' );
	$_filter_assigned	= $mainframe->getUserStateFromRequest( $_option.'filter_assigned',	'filter_assigned',	'',				'cmd' );
	$_search			= $mainframe->getUserStateFromRequest( $_option.'search',			'search',			'',				'string' );
	$_search			= JString::strtolower( $_search );

	$_limit				= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg( 'list_limit' ), 'int' );
	$_limitstart		= JRequest::getCmd( 'limitstart' );

	$_where[] = 'm.client_id = '.( int ) $client->id;

	$_joins[] = 'LEFT JOIN #__users AS u ON u.id = m.checked_out';
	$_joins[] = 'LEFT JOIN #__groups AS g ON g.id = m.access';
	$_joins[] = 'LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id';

	// used by filter
	if ( $_filter_assigned ) {
		$_joins[] = 'LEFT JOIN #__templates_menu AS t ON t.menuid = mm.menuid';
		$_where[] = 't.template = '.$_db->Quote( $_filter_assigned );
	}
	if ( $_filter_position ) {
		$_where[] = 'm.position = '.$_db->Quote( $_filter_position );
	}
	if ( $_filter_type ) {
		$_where[] = 'm.module = '.$_db->Quote( $_filter_type );
	}
	if ( $_search ) {
		$_where[] = 'LOWER( m.title ) LIKE '.$_db->Quote( '%'.$_db->getEscaped( $_search, true ).'%', false );
	}
	if ( $_filter_state ) {
		if ( $_filter_state == 'P' ) {
			$_where[] = 'm.published = 1';
		} else if ( $_filter_state == 'U' ) {
			$_where[] = 'm.published = 0';
		}
	}

	$_where		= ' WHERE ' . implode( ' AND ', $_where );
	$_join		= ' ' . implode( ' ', $_joins );
	if ( $_filter_order == 'm.ordering' ) {
		$_orderby = ' ORDER BY m.position, m.ordering '. $_filter_order_Dir;
	} else {
		$_orderby = ' ORDER BY '. $_filter_order .' '. $_filter_order_Dir .', m.ordering ASC';
	}

	// get the total number of records
	$_query = 'SELECT COUNT( DISTINCT m.id )'
	. ' FROM #__modules AS m'
	. $_join
	. $_where
	;
	$_db->setQuery( $_query );
	$_total = $_db->loadResult();

	jimport( 'joomla.html.pagination' );
	$pageNav = new JPagination( $_total, $_limitstart, $_limit );

	$_query = 'SELECT m.*, u.name AS editor, g.name AS groupname, MIN( mm.menuid ) AS pages'
	. ' FROM #__modules AS m'
	. $_join
	. $_where
	. ' GROUP BY m.id'
	. $_orderby
	;
	$_db->setQuery( $_query, $pageNav->limitstart, $pageNav->limit );
	$rows = $_db->loadObjectList();
	if ( $_db->getErrorNum() ) {
		echo $_db->stderr();
		return false;
	}

	// get list of Positions for dropdown filter
	$_query = 'SELECT m.position AS value, m.position AS text'
	. ' FROM #__modules as m'
	. ' WHERE m.client_id = '.( int ) $client->id
	. ' GROUP BY m.position'
	. ' ORDER BY m.position'
	;
	$_positions[] = JHTML::_( 'select.option',  '0', '- '. JText::_( 'Select Position' ) .' -' );
	$_db->setQuery( $_query );
	$_positions = array_merge( $_positions, $_db->loadObjectList() );
	$lists['position']	= JHTML::_( 'select.genericlist',   $_positions, 'filter_position', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', "$_filter_position" );

	// get list of Positions for dropdown filter
	$_query = 'SELECT module AS value, module AS text'
	. ' FROM #__modules'
	. ' WHERE client_id = '.( int ) $client->id
	. ' GROUP BY module'
	. ' ORDER BY module'
	;
	$_db->setQuery( $_query );
	$_types[]		= JHTML::_( 'select.option',  '0', '- '. JText::_( 'Select Type' ) .' -' );
	$_types			= array_merge( $_types, $_db->loadObjectList() );
	$lists['type']	= JHTML::_( 'select.genericlist',   $_types, 'filter_type', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', "$_filter_type" );

	// state filter
	$lists['state']	= JHTML::_( 'grid.state',  $_filter_state );

	// template assignment filter
	$_query = 'SELECT DISTINCT( template ) AS text, template AS value'.
			' FROM #__templates_menu' .
			' WHERE client_id = '.( int ) $client->id;
	$_db->setQuery( $_query );
	$_assigned[]		= JHTML::_( 'select.option',  '0', '- '. JText::_( 'Select Template' ) .' -' );
	$_assigned		= array_merge( $_assigned, $_db->loadObjectList() );
	$lists['assigned']	= JHTML::_( 'select.genericlist',   $_assigned, 'filter_assigned', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', "$_filter_assigned" );

	// table ordering
	$lists['order_Dir']	= $_filter_order_Dir;
	$lists['order']		= $_filter_order;

	// search filter
	$lists['search']= $_search;

	outputHTML( $rows, $client, $pageNav, $lists );
}
	function outputHTML( &$rows, &$client, &$page, &$lists )
	{
		$_user = & JFactory::getUser();

		$_ordering = ( $lists['order'] == 'm.ordering' || $lists['order'] == 'm.position' );

		JHTML::_( 'behavior.tooltip' );
		$_plugin_params = getPluginParams( 'system', 'modulesanywhere', JPATH_SITE.DS.'plugins'.DS.'system'.DS.'modulesanywhere.xml' );

		// Load plugin language
		$_lang =& JFactory::getLanguage();
		$_lang->load( 'plg_editors-xtd_modulesanywhere', JPATH_ADMINISTRATOR );
		$_lang->load( 'plg_system_modulesanywhere', JPATH_ADMINISTRATOR );
?>

	<form action="" method="post" name="adminForm" id="adminForm">
		<fieldset>
			<div style="float: left">
				<h1><?php echo JText::_( 'Modules Anywhere' ); ?></h1>
			</div>
			<div style="float: right">
				<div class="button2-left"><div class="blank">
					<a rel="" onclick="window.parent.document.getElementById('sbox-window').close();" href="javascript://" title="<?php echo JText::_('Cancel') ?>"><?php echo JText::_('Cancel') ?></a>
				</div></div>
			</div>
		</fieldset>
			<p><?php echo JText::_('Click on one of the modules links') ?></p>
		<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<td width="1%" nowrap="nowrap"><?php echo JText::_( 'Module style' ); ?>:</td>
					<td>
						<select name="style" class="inputbox">
						<?php
							$_style = JRequest::getCmd( 'style' );
							if ( !$_style ) {
								$_style = $_plugin_params->get( 'style' );
							}

							echo '
								<option '.( ( $_style == 'none' ) ? 'selected="selected" value=""' : 'value="none"' ).'>'.
									JText::_( 'No wrapping - raw output (none)' ).'</option>
								<option '.( ( $_style == 'table' ) ? 'selected="selected" value=""' : 'value="table"' ).'>'.
									JText::_( 'Wrapped by Table - Column (table)' ).'</option>
								<option '.( ( $_style == 'horz' ) ? 'selected="selected" value=""' : 'value="horz"' ).'>'.
									JText::_( 'Wrapped by Table - Horizontal (horz)' ).'</option>
								<option '.( ( $_style == 'xhtml' ) ? 'selected="selected" value=""' : 'value="xhtml"' ).'>'.
									JText::_( 'Wrapped by Divs (xhtml)' ).'</option>
								<option '.( ( $_style == 'rounded' ) ? 'selected="selected" value=""' : 'value="rounded"' ).'>'.
									JText::_( 'Wrapped by Multiple Divs (rounded)' ).'</option>
							';
						?>
						</select>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo JText::_( 'Filter' ); ?>:</td>
					<td>
						<input style="float:left;" type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
						<div class="button2-left"><div class="blank">
							<a rel="" onclick="this.form.submit();" href="javascript://" title="<?php echo JText::_('Go') ?>"><?php echo JText::_('Go') ?></a>
						</div></div>
						<div class="button2-left"><div class="blank">
							<a rel="" onclick="document.getElementById( 'search' ).value='';this.form.submit();" href="javascript://" title="<?php echo JText::_('Reset') ?>"><?php echo JText::_('Reset') ?></a>
						</div></div>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<?php
						echo $lists['assigned'];
						echo $lists['position'];
						echo $lists['type'];
						echo $lists['state'];
						?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_( 'grid.sort',   'ID', 'm.id', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th class="title">
						<?php echo JHTML::_( 'grid.sort', 'Module Name', 'm.title', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th nowrap="nowrap" width="7%">
						<?php echo JHTML::_( 'grid.sort',   'Position', 'm.position', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th nowrap="nowrap" width="7%">
						<?php echo JHTML::_( 'grid.sort', 'Published', 'm.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th nowrap="nowrap" width="1%">
						<?php echo JHTML::_( 'grid.sort', 'Order', 'm.ordering', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<?php
					if ( $client->id == 0 ) {
						?>
						<th nowrap="nowrap" width="7%">
							<?php echo JHTML::_( 'grid.sort', 'Access', 'groupname', @$lists['order_Dir'], @$lists['order'] ); ?>
						</th>
						<?php
					}
					?>
					<th nowrap="nowrap" width="5%">
						<?php echo JHTML::_( 'grid.sort',   'Pages', 'pages', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th nowrap="nowrap" width="10%"  class="title">
						<?php echo JHTML::_( 'grid.sort',   'Type', 'm.module', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo ( $client->id == 0 ) ? '8' : '7'; ?>">
						<?php
							$pagination = str_replace( 'index.php?', 'plugins/editors-xtd/modulesanywhere/elements/modulesanywhere.page.php?name='.JRequest::getCmd( 'name', 'text' ).'&', $page->getListFooter() );
							$pagination = str_replace( 'index.php', 'plugins/editors-xtd/modulesanywhere/elements/modulesanywhere.page.php?name='.JRequest::getCmd( 'name', 'text' ), $pagination );
							echo $pagination;
						?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$_k = 0;
			for ( $_i=0, $_n=count( $rows ); $_i < $_n; $_i++ ) {
				$_row = &$rows[$_i];

				if ( $_row->published ) {
					$_img = 'tick.png';
					$_alt = JText::_( 'Published' );
				} else {
					$_img = 'publish_x.png';
					$_alt = JText::_( 'Unpublished' );
				}
				$_published = '<img src="'.JURI::base( true ).'/plugins/editors-xtd/modulesanywhere/images/'. $_img .'" border="0" alt="'. $_alt .'" />'
				?>
				<tr class="<?php echo "row$_k"; ?>">
					<td align="right">
						<?php echo '<label class="hasTip" title="{module '.$_row->id.'}"><a href="javascript://" onclick="modulesanywhere_jInsertEditorText( \''.$_row->id.'\' )">'.$_row->id.'</a></label>';?>
					</td>
					<td>
						<?php echo '<label class="hasTip" title="{module '.$_row->title.'}"><a href="javascript://" onclick="modulesanywhere_jInsertEditorText( \''.$_row->title.'\' )">'.$_row->title.'</a></label>'; ?>
					</td>
					<td align="center">
						<?php echo '<label class="hasTip" title="{modulepos '.$_row->position.'}"><a href="javascript://" onclick="modulesanywhere_jInsertEditorText( \''.$_row->position.'\', 1 )">'.$_row->position.'</a></label>'; ?>
					</td>
					<td align="center">
						<?php echo $_published; ?>
					</td>
					<td align="center">
						<?php echo $_row->ordering; ?>
					</td>
					<?php
					if ( $client->id == 0 ) {
						?>
						<td align="center">
							<?php
								if ( !$_row->access )  {
									$_color_access = 'style="color: green;"';
								} else if ( $row->access == 1 ) {
									$_color_access = 'style="color: red;"';
								} else {
									$_color_access = 'style="color: black;"';
								}
								echo '<span '.$_color_access.'>'.JText::_( $_row->groupname ).'</span>';
							?>
						</td>
						<?php
					}
					?>
					<td align="center">
						<?php
						if ( is_null( $_row->pages ) ) {
							echo JText::_( 'None' );
						} else if ( $_row->pages > 0 ) {
							echo JText::_( 'Varies' );
						} else {
							echo JText::_( 'All' );
						}
						?>
					</td>
					<td>
						<?php echo $_row->module ? $_row->module : JText::_( 'User' ); ?>
					</td>
				</tr>
				<?php
				$_k = 1 - $_k;
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="name" value="<?php echo JRequest::getCmd( 'name', 'text' ); ?>" />
		<input type="hidden" name="client" value="<?php echo $client->id;?>" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
	</form>
<?php
}
?>