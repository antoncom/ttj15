<?php
/**
 * Highslide Configuration Controller for the component
 *
 * @license		GNU/GPL
 */

/**
 * Utility class for creating HTML Grids
 */
class JHTMLHsConfig
{
	/**
	 * Displays the publishing state legend for articles
	 */
	function Legend( )
	{
		?>
		<table cellspacing="0" cellpadding="4" border="0" align="center">
		<tr align="center">
			<td>
			<img src="images/publish_y.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Not Current' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Published, but is' ); ?> <u><?php echo JText::_( 'Not Current' ); ?></u> |
			</td>
			<td>
			<img src="images/publish_g.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Published' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Published and is' ); ?> <u><?php echo JText::_( 'Current' ); ?></u> |
			</td>
			<td>
			<img src="images/publish_x.png" width="16" height="16" border="0" alt="<?php echo JText::_( 'Unpublished' ); ?>" />
			</td>
			<td>
			<?php echo JText::_( 'Not Published' ); ?>
			</td>

		</tr>
		<tr>
			<td colspan="10" align="center">
			<?php echo JText::_( 'Click on icon to toggle state.' ); ?>
			</td>
		</tr>
		</table>
		<?php
	}
}