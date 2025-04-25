<?php
defined('_JEXEC') or die('Restricted access'); 
if ( $this->params->def( 'show_page_title', 1 ) ) { 
	echo '<div class="componentheading'. $this->params->get( 'pageclass_sfx' ).'">'. $this->params->get('page_title').'</div>';
}

echo '<div id="phoca-doc-sections-box">';

if (!empty($this->section)) {
	$i = 1;
	foreach ($this->section as $value) {
		
		// Categories
		$numDoc 	= 0;
		$catOutput 	= '';
		foreach ($value->categories as $valueCat) {
			$catOutput 	.= '<p class="pdoc-category">';
			$catOutput 	.= '<a href="'. JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($valueCat->id, $valueCat->alias, $value->id)).'">'. $valueCat->title.'</a>';
			
			if ($this->tmpl['displaynumdocsecs'] == 1) {
				$catOutput  .=' <small>('.$valueCat->numdoc .')</small>';
			}
			$catOutput 	.= '</p>' . "\n";
			$numDoc = (int)$valueCat->numdoc + (int)$numDoc;
		}
		
		echo '<div class="pdoc-sections"><div><div><div><h3>';
		echo '<a href="'. JRoute::_(PhocaDocumentationHelperRoute::getSectionRoute($value->id, $value->alias)).'">'. $value->title.'</a>';
		
		if ($this->tmpl['displaynumdocsecsheader'] == 1) {
			echo ' <small>('.$value->numcat.'/' . $numDoc .')</small>';
		}
		echo '</h3>';
		echo $catOutput;	
		echo '</div></div></div></div>';
		if ($i%3==0) {
			echo '<div style="clear:both"></div>';
		}
		$i++;
		
	}
}
echo '</div>';

if (!empty($this->mostvieweddocs)) {
	echo '<div class="phoca-doc-hr" style="clear:both">&nbsp;</div>';
	echo '<div id="phoca-doc-most-viewed-box">';
    echo '<div class="pdoc-documents"><h3>'. JText::_('Most viewed documents').'</h3>';
    foreach ($this->mostvieweddocs as $value) {
    echo '<p class="pdoc-document">';
		echo '<a href="'. JRoute::_(PhocaDocumentationHelperRoute::getArticleRoute($value->id, $value->alias, $value->categoryid, $value->categoryalias, $this->tmpl['article_itemid'] )).'">'. $value->title.'</a> <small>(' .$value->sectiontitle. '/'.$value->categorytitle.')</small>';
        echo '</p>' . "\n";
    }
    echo '</div></div>';
} else {
	echo '<div style="clear:both"></div>';
}
echo $this->tmpl['id'];
?>

