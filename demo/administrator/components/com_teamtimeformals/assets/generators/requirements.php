<?php

// Техническое задание
defined('_JEXEC') or die('Restricted access');

$result_content = "";

$todo_data = $rows_todos[0];

$tpl->setVariable("todo_title", $todo_data->title);
$tpl->setVariable("todo_text", $todo_data->description);

// return generated content
$result_content .= $tpl->get();