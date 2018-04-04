<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php

	JToolBarHelper::title( JText::_( 'Control panel' ), 'cpanel.png' );
	jceToolbarHelper::help( 'cpanel' );

	JHTML::stylesheet('icons.css', 'administrator/components/com_teamtime/css/');
?>
<table class="admintable">
    <tr>
        <td width="55%" valign="top" colspan="2">
		<div id="cpanel">
			<?php
				JCEHelper::quickiconButton(
					'index.php?option=com_teamtime&amp;controller=config',
					'components/com_teamtime/assets/images/icon-48-config.png',
					JText::_( 'TEAMLOG_CONFIGURATION' ) );

				foreach(TeamTime::addonsList() as $name)
					print TeamTime::_("get_addon_button_{$name}", "");
			?>
		</div>
        <div class="clr"></div>
        </td>
    </tr>
	<tr>
    	<td>
        	<table class="admintable">
            	
              
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'Documentation' );?>
                    </td>
                    <td>
                        <a href="http://www.teamtime.info" target="_new">www.teamtime.info</a>
                    </td>
                </tr>
                
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'License' );?>
                    </td>
                    <td>GNU/GPL</td>
                </tr>
                 <tr>
                    <td class="key">
                        <?php echo JText::_( 'Component Version' );?>
                    </td>
                    <td>
                        <?=$this->component_version?>
                    </td>
                </tr>                
                
            </table>
        </td>
    </tr>
</table>