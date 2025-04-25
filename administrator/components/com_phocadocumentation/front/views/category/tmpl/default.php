<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if ( $this->params->def( 'show_page_title', 1 ) ) {
	echo '<div class="componentheading'.$this->params->get( 'pageclass_sfx' ).'">'. $this->params->get('page_title'). '</div>';
} 

echo '<div id="phoca-doc-category-box">';

if (!empty($this->section[0])) {
	echo '<div class="pdoc-category">';
	if ($this->tmpl['display_up_icon'] == 1) {
		echo '<div class="pdoctop"><a title="'.JText::_('Section').'" href="'. JRoute::_(PhocaDocumentationHelperRoute::getSectionRoute($this->section[0]->id, $this->section[0]->alias)).'" >'.JHTML::_('image', 'components/com_phocadocumentation/assets/images/up.png', JText::_('Up')).  '</a></div>';
	}
} else {
	echo '<div class="pdoc-category">'
		.'<div class="pdoctop"></div>';
}

if (!empty($this->category[0])) {
	echo '<h3>'.$this->category[0]->title. '</h3>';

	// Description
	echo '<div class="contentpane'.$this->params->get( 'pageclass_sfx' ).'">';
	if ( (isset($this->tmpl['image']) && $this->tmpl['image'] !='') || (isset($this->category[0]->description) && $this->category[0]->description != '' && $this->category[0]->description != '<p>&#160;</p>')) {
		echo '<div class="contentdescription'.$this->params->get( 'pageclass_sfx' ).'">';
		if ( isset($this->tmpl['image']) ) {
			echo $this->tmpl['image'];
		}
		echo $this->category[0]->description
			.'</div><p>&nbsp;</p>';
	}
	echo '</div>';
			
	if (!empty($this->documentlist)) {	
		foreach ($this->documentlist as $valueDoc) {
			echo '<p class="pdoc-document">';
			echo '<a href="'. JRoute::_(PhocaDocumentationHelperRoute::getArticleRoute($valueDoc->id, $valueDoc->alias, $valueDoc->categoryid, $valueDoc->categoryalias, $this->tmpl['article_itemid'] )).'">'. $valueDoc->title.'</a>';
			echo '</p>' . "\n";
		}
	}
	echo '</div>';

} else {
	echo '<h3>&nbsp;</h3>';
	echo '</div>';
}
echo $this->tmpl['id'];
echo '</div>';
?>

