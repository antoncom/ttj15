<?php
defined('_JEXEC') or die('Restricted access');
if ( $this->params->def( 'show_page_title', 1 ) ) { 
	echo '<div class="componentheading'. $this->params->get( 'pageclass_sfx' ).'">'. $this->params->get('page_title').'</div>';
}
if (!isset($this->section[0])) {
	return JError::raiseError( 404, JText::_( 'Document not found') );
}

echo '<div id="phoca-doc-section-box">';

echo '<div class="pdoc-section">';
if ($this->tmpl['display_up_icon'] == 1) {
	echo '<div class="pdoctop"><a title="'.JText::_('Sections').'" href="'. JRoute::_(PhocaDocumentationHelperRoute::getSectionsRoute()).'" >'.JHTML::_('image', 'components/com_phocadocumentation/assets/images/up.png', JText::_('Up')).  '</a></div>';
}
echo '<h3>'.$this->section[0]->title. '</h3>';

// Description
echo '<div class="contentpane'.$this->params->get( 'pageclass_sfx' ).'">';
if ( (isset($this->tmpl['image']) && $this->tmpl['image'] !='') || (isset($this->section[0]->description) && $this->section[0]->description != '' && $this->section[0]->description != '<p>&#160;</p>')) {
	echo '<div class="contentdescription'.$this->params->get( 'pageclass_sfx' ).'">';
	if ( isset($this->tmpl['image']) ) {
		echo $this->tmpl['image'];
	}
	echo $this->section[0]->description
		.'</div><p>&nbsp;</p>';
}
echo '</div>';
    
if (!empty($this->categorylist)) {  
    foreach ($this->categorylist as $valueCat) {
        echo '<p class="pdoc-category">';
        echo '<a href="'. JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($valueCat->id, $valueCat->alias, $this->section[0]->id )).'">'. $valueCat->title.'</a>';
        echo ' <small>('.$valueCat->numdoc.')</small></p>' . "\n";
    }
} else {
	echo '<p>&nbsp;</p><p>&nbsp;</p>';
}
echo '</div>';


if (!empty($this->mostvieweddocs)) {
	echo '<div class="phoca-doc-hr" style="clear:both">&nbsp;</div>';
	echo '<div id="phoca-doc-most-viewed-box">';
    echo '<div class="pdoc-documents"><h3>'. JText::_('Most viewed documents section').'</h3>';
    foreach ($this->mostvieweddocs as $value) {
    echo '<p class="pdoc-document">';
		echo '<a href="'. JRoute::_(PhocaDocumentationHelperRoute::getArticleRoute($value->id, $value->alias, $value->categoryid, $value->categoryalias, $this->tmpl['article_itemid'] )).'">'. $value->title.'</a> <small>(' .$value->sectiontitle. '/'.$value->categorytitle.')</small>';
        echo '</p>' . "\n";
    }
    echo '</div></div>';
}
echo $this->tmpl['id'];
echo '</div>';
?>
