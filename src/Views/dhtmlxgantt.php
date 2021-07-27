<?php
/*
 * Yurii's Gantt Plugin
 *
 * Copyright (C) 2020 Yurii K.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses
 */
/**
 * https://docs.dhtmlx.com/gantt/api__gantt_autosize_config.html
 *
 * @var array $database
 * @var string $pluginName
 * @var string $lang
 * @var $baseUrl
 */

use dokuwiki\plugin\yuriigantt\src\Driver\Embedded as EmbeddedDriver;

?>
<link rel="stylesheet" href="<?= $baseUrl ?>lib/plugins/<?= $pluginName; ?>/3rd/dhtmlxgantt/dhtmlxgantt.css">
<script src="<?= $baseUrl ?>lib/plugins/<?= $pluginName; ?>/3rd/dhtmlxgantt/dhtmlxgantt.js"></script>
<div id="<?= $pluginName; ?>"></div>
<script>
    let database = <?= json_encode($database); ?>;

    gantt.i18n.setLocale('<?= $lang; ?>')
    gantt.config.autosize = true
    gantt.config.order_branch = true
    gantt.config.order_branch_free = true

    gantt.init('<?=$pluginName;?>')

    if (database.dsn === '<?= EmbeddedDriver::DSN ?>') {
        gantt.config.cascade_delete = false; // optimization
        gantt.parse(database.gantt)
    } else {
        throw new Error('NOT SUPPORTED DSN!')
        //gantt.load('..URL..')
    }

    let dp = gantt.createDataProcessor({
        task: {
            create: function (data) {
                restCall('create', 'task', data)
            },
            update: function (data, id) {
                restCall('update', 'task', data, id)
            },
            delete: function (id) {
                restCall('delete', 'task', null, id)
            }
        },
        link: {
            create: function (data) {
                restCall('create', 'link', data)
            },
            update: function (data, id) {
                restCall('update', 'link', data, id)
            },
            delete: function (id) {
                restCall('delete', 'link', null, id)
            }
        }
    });
    dp.attachEvent("onAfterUpdate", function(id, action, tid, response){
        if(action === 'error'){
            console.warn('ERROR', response)
        }
    });

    function restCall(action, entity, data, id) {
        gantt.ajax.post('<?= $baseUrl . 'lib/exe/ajax.php'; ?>', {
            call: 'plugin_<?=$pluginName;?>',
            csrf: '<?= getSecurityToken() ; ?>',
            payload: {
                pageId: database.pageId,
                version: database.version,
                action: action,
                entity: entity,
                data: data,
                id: id,
                test: true
            }
        }).then(function(response){
            var res = JSON.parse(response.responseText);
            console.log(res)
            if (res && res.status == "ok") {
                // response is ok
                console.log(res)
            }
        })
    }
</script>
