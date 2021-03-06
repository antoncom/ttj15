<?php
/**
* @version		$Id: advanced.php 221 2011-06-11 17:30:33Z happy_noodle_boy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
        <table border="0" cellpadding="2">
            <tr>
                <td><label for="style" class="hastip" title="<?php echo WFText::_('WF_LABEL_STYLE_DESC');?>"><?php echo WFText::_('WF_LABEL_STYLE');?></label></td>
                <td colspan="2"><input id="style" type="text" value="" onchange="ImageManagerDialog.setStyles();" /></td>
            </tr>
            <tr>
                <td><label for="classlist" class="hastip" title="<?php echo WFText::_('WF_LABEL_CLASS_LIST_DESC');?>"><?php echo WFText::_('WF_LABEL_CLASS_LIST');?></label></td>
                <td colspan="2">
                    <select id="classlist" onchange="ImageManagerDialog.setClasses(this.value);">
                        <option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
                    </select>
                </td>
            </tr>
            <tr>
                 <td><label for="title" class="hastip" title="<?php echo WFText::_('WF_LABEL_CLASSES_DESC');?>"><?php echo WFText::_('WF_LABEL_CLASSES');?></label></td>
                 <td colspan="3"><input id="classes" type="text" value="" /></td>
            </tr>
            <tr>
                 <td><label for="title" class="hastip" title="<?php echo WFText::_('WF_LABEL_TITLE_DESC');?>"><?php echo WFText::_('WF_LABEL_TITLE');?></label></td>
                 <td colspan="3"><input id="title" type="text" value="" /></td>
            </tr>
            <tr>
                <td><label for="id" class="hastip" title="<?php echo WFText::_('WF_LABEL_ID_DESC');?>"><?php echo WFText::_('WF_LABEL_ID');?></label></td>
                <td colspan="2"><input id="id" type="text" value="" /></td>
            </tr>

            <tr>
                <td><label for="dir" class="hastip" title="<?php echo WFText::_('WF_LABEL_DIR_DESC');?>"><?php echo WFText::_('WF_LABEL_DIR');?></label></td>
                <td colspan="2">
                    <select id="dir" onchange="ImageManagerDialog.updateStyles();">
                            <option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
                            <option value="ltr"><?php echo WFText::_('WF_OPTION_LTR');?></option>
                            <option value="rtl"><?php echo WFText::_('WF_OPTION_RTL');?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><label for="lang" class="hastip" title="<?php echo WFText::_('WF_LABEL_LANG_DESC');?>"><?php echo WFText::_('WF_LABEL_LANG');?></label></td>
                <td colspan="2"><input id="lang" type="text" value="" /></td>
            </tr>

            <tr>
                <td><label for="usemap" class="hastip" title="<?php echo WFText::_('WF_LABEL_USEMAP_DESC');?>"><?php echo WFText::_('WF_LABEL_USEMAP');?></label></td>
                <td colspan="2"><input id="usemap" type="text" value="" /></td>
            </tr>

            <tr>
                <td><label for="longdesc" class="hastip" title="<?php echo WFText::_('WF_LABEL_LONGDESC_DESC');?>"><?php echo WFText::_('WF_LABEL_LONGDESC');?></label></td>
                <td><input id="longdesc" type="text" value="" class="browser image" /></td>
                <td id="longdesccontainer">&nbsp;</td>
            </tr>
        </table>