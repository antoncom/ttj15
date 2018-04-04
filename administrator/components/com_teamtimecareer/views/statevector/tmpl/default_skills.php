<?php
defined('_JEXEC') or die('Restricted access');
?>

<table class="adminlist" id="skillsTable">
  <thead>
    <tr>
      <th width="20">
        <input type="checkbox" name="toggle" value="" 
               onclick="checkAll(<?php echo count($this->skillItems); ?>);" />
      </th>

      <th><?= JText::_('Skill name') ?></th>
      <th><?= JText::_('State user value') ?></th>      
      <th><?= JText::_('Skill value') ?></th>      
    </tr>

  <tbody>
    <?php
    foreach ($this->skillItems as $i => $row) {
      if (!isset($this->targetBalance[$row->id]) || $this->targetBalance[$row->id] <= 0) {
        continue;
      }

      $checked = JHTML::_('grid.id', $i, $row->id);
      ?>
      <tr id="skill<?= $row->id ?>" class="<?php echo "row$k"; ?>">

        <td class="skillcell_check" align="center">
          <?php echo $checked; ?>
          <input type="hidden" name="skill_value[<?= $row->id ?>]" 
                 value="<?= $row->state ?>" />
          <input type="hidden" name="skill_title[<?= $row->id ?>]" 
                 value="<?= htmlspecialchars($row->title) ?>" />
          <input type="hidden" name="skill_checked[<?= $row->id ?>]" 
                 value="<?= $row->state >= $row->num ? 1 : 0
        /* isset($this->markedSkills[$row->id]) ? 1 : 0 */ ?>" />
        </td>

        <td class="skillcell_title" width="60%">
          <?= $row->title ?>
        </td>

        <td class="skillcell_new" align="center">
          <input type="text" class="new_skill_value"
                 name="new_skill_value[<?= $row->id ?>]" 
                 value="<?= $row->state ?>" size="10">
        </td>

        <td  class="skillcell_num" align="center">          
          <?= $row->num ?>
          <input type="hidden" value="<?= $row->num ?>" />
        </td>

      </tr>
      <?php
      $k = 1 - $k;
    }
    ?>        

  </tbody>
</table>

<input type="hidden" name="boxchecked" value="0" />