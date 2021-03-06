<table class="migration-table table table-bordered table-striped">
    <thead>
    <tr>
        <th><?=t('Handle')?></th>
        <th><?=t('Name')?></th>
        <th><?=t('Active')?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($collection->getEditors() as $editor) {
    $validator = $editor->getPublisherValidator();
    ?>
        <tr <?php if ($validator->skipItem()) {
    ?>class="migration-item-skipped"<?php 
}
    ?>>
            <td><?=$editor->getHandle()?></td>
            <td><?=$editor->getName()?></td>
            <td><?=$editor->getIsActive() ? t('Yes') : t('No') ?></td>
        </tr>
    <?php 
} ?>
    </tbody>
</table>
