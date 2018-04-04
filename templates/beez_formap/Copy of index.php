<?php
/**
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

$url = clone(JURI::getInstance());
$showRightColumn = $this->countModules('user1 or user2 or right or top');
$showRightColumn &= JRequest::getCmd('layout') != 'form';
$showRightColumn &= JRequest::getCmd('task') != 'edit'
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_formap/css/template.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_formap/css/position.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_formap/css/layout.css" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_formap/css/print.css" type="text/css" media="Print" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_formap/css/general.css" type="text/css" />
	<?php if($this->direction == 'rtl') : ?>
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/beez_formap/css/template_rtl.css" type="text/css" />
	<?php endif; ?>
	<!--[if lte IE 6]>
		<link href="<?php echo $this->baseurl ?>/templates/beez_formap/css/ieonly.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	<!--[if IE 7]>
		<link href="<?php echo $this->baseurl ?>/templates/beez_formap/css/ie7only.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/beez_formap/javascript/md_stylechanger.js"></script>
</head>
<body>
	<div id="all">
		<div id="header">
		<noindex>
		<!--[if IE 7]>
			<div style="height:1px;"></div><![endif]-->
		    <h1 id="logo">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background:url(/images/elka_a.jpg) no-repeat top right;"><tr valign="bottom"> 
<td valign="top"><img src="/images/elka.gif" width="110" height="47" alt=""><center>
<img src="/images/logo.gif" width="210" height="114" alt="Эврика" border="0" style="margin-top:5px;"></center><div id="logoAddress">424031, Республика Марий Эл, г. Йошкар-Ола, ул. Чехова, д.73, Тел: 8 (8362) 46-90-83, 46-90-74, Email: info@evrikahotel.ru</div></td></tr></table>
			</h1>

			<div id="headpic">
			</div>
<?php /* 
			<div id="fontsize">
				<script type="text/javascript">
				//<![CDATA[
					document.write('<h3><?php echo JText::_('FONTSIZE'); ?></h3><p class="fontsize">');
					document.write('<a href="index.php" title="<?php echo JText::_('Increase size'); ?>" onclick="changeFontSize(2); return false;" class="larger"><?php echo JText::_('bigger'); ?></a><span class="unseen">&nbsp;</span>');
					document.write('<a href="index.php" title="<?php echo JText::_('Decrease size'); ?>" onclick="changeFontSize(-2); return false;" class="smaller"><?php echo JText::_('smaller'); ?></a><span class="unseen">&nbsp;</span>');
					document.write('<a href="index.php" title="<?php echo JText::_('Revert styles to default'); ?>" onclick="revertStyles(); return false;" class="reset"><?php echo JText::_('reset'); ?></a></p>');
				//]]>
				</script>
			</div>

			<jdoc:include type="modules" name="user3" />
			<jdoc:include type="modules" name="user4" />
*/ ?>

			<div class="wrap">&nbsp;</div>
			</noindex>
		</div><!-- end header -->
					

		<div id="<?php echo $showRightColumn ? 'contentarea2' : 'contentarea'; ?>">
			<a name="mainmenu"></a>
			<div id="left">
			<noindex>
				<jdoc:include type="modules" name="left" style="beezDivision" headerLevel="3" />
			</noindex>
			</div><!-- left -->

			<a name="content"></a>
			<div id="wrapper">
			<div id="<?php echo $showRightColumn ? 'main2' : 'main'; ?>">
				<?php if ($this->getBuffer('message')) : ?>
				<div class="error">
					<h2>
						<?php echo JText::_('Message'); ?>
					</h2>
					<jdoc:include type="message" />
				</div>
				<?php endif; ?>
							<div id="breadcrumbs">
				<p>
					<jdoc:include type="modules" name="breadcrumb" />
				</p>
			</div>
				<jdoc:include type="modules" name="mainannounce" />
				<jdoc:include type="component" />
			</div><!-- end main or main2 -->

			<?php if ($showRightColumn) : ?>
			<div id="right">

				<a name="additional"></a>
				<h2 class="unseen">
					<?php echo JText::_('Additional Information'); ?>
				</h2>

				<jdoc:include type="modules" name="top" style="beezDivision" headerLevel="3" />
				<jdoc:include type="modules" name="user1" style="beezDivision" headerLevel="3" />
				<jdoc:include type="modules" name="user2" style="beezDivision" headerLevel="3" />
				<jdoc:include type="modules" name="right" style="beezDivision" headerLevel="3" />

			</div><!-- right -->
			<?php endif; ?>

			<div class="wrap"></div>
			</div><!-- wrapper -->
		</div><!-- contentarea -->

		<div id="footer">
			<p class="syndicate">
				<jdoc:include type="modules" name="syndicate" />
			</p>
            <div class="wrap"></div>
		</div><!-- footer -->
	</div><!-- all -->

	<jdoc:include type="modules" name="debug" />
	<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='http://counter.yadro.ru/hit?t26.6;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";h"+escape(document.title.substring(0,80))+";"+Math.random()+
"' alt='' title='LiveInternet: показано число посетителей за"+
" сегодня' "+
"border=0 width=88 height=15><\/a>")//--></script><!--/LiveInternet-->
</body>
</html>