<?php
/**
 * @var ThemeIcons        $icons
 * @var callable          $_getvar
 * @var ConfigData        $configData
 * @var AccountSearchItem $account
 * @var DataGridTab       $data
 */

use SP\Config\ConfigData;
use SP\Core\UI\ThemeIcons;
use SP\Html\DataGrid\DataGridTab;
use SP\Services\Account\AccountSearchItem;

if (!isset($data)) {
    $data = $_getvar('data');
}
?>

<div id="data-search-header">
    <ul class="header-columns">
        <?php foreach ($data->getHeader()->getSortFields() as $field):
            $isActive = $data->getPager()->getSortKey() == $field->getSortKey();
            $sortOrder = $data->getPager()->getSortOrder();
            // 3-state cycle: off -> ascending (1) -> descending (0) -> off (clear)
            if (!$isActive) {
                $nextDir = 1; // First click: ascending
            } elseif ($sortOrder === 1) {
                $nextDir = 0; // Currently ascending, next: descending
            } else {
                $nextDir = -1; // Currently descending, next: clear filter
            }
        ?>
            <li class="header-column sortable <?php echo $isActive ? 'sorted' : ''; ?>"
                data-start="<?php echo $data->getPager()->getLimitStart(); ?>"
                data-key="<?php echo $field->getSortKey(); ?>"
                data-dir="<?php echo $nextDir; ?>"
                data-current-dir="<?php echo $isActive ? $sortOrder : ''; ?>">
                <span class="column-name <?php echo $isActive ? 'active' : ''; ?>">
                    <?php echo $field->getName(); ?>
                </span>
                <?php if ($isActive): ?>
                    <i class="material-icons sort-icon"><?php echo $sortOrder === 1 ? 'arrow_drop_up' : 'arrow_drop_down'; ?></i>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
