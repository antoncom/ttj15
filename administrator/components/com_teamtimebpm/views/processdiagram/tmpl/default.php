<?php
defined('_JEXEC') or die('Restricted access');

$assetsUrl = "/" . URL_MEDIA_COMPONENT_ASSETS;
?>

<!--[if lte IE 7]>
<link type="text/css" rel="stylesheet"
	href="administrator/components/com_teamtimebpm/assets/css/diagram_elements_ie.css" />
<![endif]-->

<style>
	body {
		margin:0px;
		padding:0px;
	}
</style>

<!-- swimlane panel layout -->

<div id="SwimlanePanel-cols-container">
	<ul id="SwimlanePanel-cols">
	</ul>
</div>

<div id="SwimlanePanel-rows-container">
	<ul id="SwimlanePanel-rows">
	</ul>
</div>

<div id="SwimlanePanel-scrollarea">
	<div id="SwimlanePanel-paintarea">
	</div>
</div>

<!-- /swimlane panel layout -->

<!-- swimlane panel blocks menu -->

<div id="SwimlanePanel-blocksmenu" class="bpmn_insert_menu">
	<table>
		<tbody>
			<tr>
				<td id="SwimlanePanel-blocksmenu-insert_block"
						class="bpmn_insert_menu_header" colspan="5">Insert</td>
			</tr>
			<tr>
				<td class="bpmn_insert_menu_item cmdInsertActivity">
					<img src="<?= $assetsUrl ?>images/icons/block.png"/>
				</td>
				<td class="bpmn_insert_menu_item cmdInsertConditionXOR">
					<img style="height: 28px;" src="<?= $assetsUrl ?>images/icons/gateway_xor.png"/>
				</td>
				<td class="bpmn_insert_menu_item cmdInsertSubprocess">
					<img style="height: 18px;" src="<?= $assetsUrl ?>images/icons/subprocess.png"/>
				</td>
				<td class="bpmn_insert_menu_item cmdInsertMessage">
					<img src="<?= $assetsUrl ?>images/icons/message.png"/>
				</td>
				<td class="bpmn_insert_menu_item cmdMoreBlocks">
					<div></div>
				</td>
			</tr>

			<!-- hidden blocks -->

			<tr class="bpmn_insert_menu_hidden">
				<td class="bpmn_insert_divider" colspan="5"></td>
			</tr>
			<tr class="bpmn_insert_menu_hidden">
				<td class="bpmn_insert_menu_item cmdInsertConditionAND">
					<img style="height: 28px;" src="<?= $assetsUrl ?>images/icons/gateway_and.png"/>
				</td>
				<td class="bpmn_insert_menu_item cmdInsertConditionOR">
					<img style="height: 28px;" src="<?= $assetsUrl ?>images/icons/gateway_or.png"/>
				</td>
				<td class="bpmn_insert_menu_item cmdInsertLinkedSubprocess">
					<img style="height: 18px;" src="<?= $assetsUrl ?>images/icons/linked-subprocess.png"/>
				</td>
			</tr>
			<tr class="bpmn_insert_menu_hidden">
				<td class="bpmn_insert_menu_item cmdInsertTimer">
					<img src="<?= $assetsUrl ?>images/icons/timer.png"/>
				</td>
				<td class="bpmn_insert_menu_item cmdInsertException">
					<img src="<?= $assetsUrl ?>images/icons/exception.png"/>
				</td>
				<td class="bpmn_insert_menu_item cmdInsertEnd">
					<img src="<?= $assetsUrl ?>images/icons/start-end.png"/>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<!-- /swimlane panel blocks menu -->