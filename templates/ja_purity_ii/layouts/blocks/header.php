<div id="ja-header" class="wrap">
<div class="main" style="background-image: url(<?php /*echo $this->templateurl(); ?>/images/header/<?php echo $this->getRandomImage($this->templatepath().DS.'images/header'); */?>);">
<div class="inner clearfix">

	<div class="ja-headermask">&nbsp;</div>

	<!--<?php
	$siteName = $this->sitename();
	if ($this->getParam('logoType')=='image'): ?>-->
	<!--<?php else:
	$logoText = (trim($this->getParam('logoType-text-logoText'))=='') ? $config->sitename : $this->getParam('logoType-text-logoText');
	$sloganText = (trim($this->getParam('logoType-text-sloganText'))=='') ? JText::_('SITE SLOGAN') : $this->getParam('logoType-text-sloganText');?>-->
	<div class="logo-text">
		<h1 class="logo">
		<a href="index.php" title="<?php echo $siteName; ?>"><span><?php echo $siteName; ?></span></a>
	</h1>
		<h1 class="logo-title"><a href="index.php" title="<?php echo $siteName; ?>"><span><?php echo $siteName; ?></span></a></h1>
		<!--<p class="site-slogan"><?php echo $sloganText;?></p>-->
	</div>
	<!--<?php endif; ?>-->
	
	<!--<?php $this->loadBlock('usertools/screen') ?>
	<?php $this->loadBlock('usertools/font') ?>-->
	
	<?php if($this->countModules('search')) : ?>
	<div id="ja-search">
		<jdoc:include type="modules" name="search" />
	</div>
	<?php endif; ?>

</div>

</div>
</div>
