<?php

defined('_JEXEC') or die('Restricted access');

// return generated content
$tpl->setCurrentBlock();
$tpl->touchBlock("__global__"); // use if not set variables

$result_content = $tpl->get();