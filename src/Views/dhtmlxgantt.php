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
<style>
.weekend{background: #ebebeb !important}
</style>
<div id="<?= $pluginName; ?>"></div>
<script>
    let database = <?= json_encode($database); ?>;

    gantt.i18n.setLocale('<?= $lang; ?>')
    gantt.config.autosize = true
    gantt.config.min_column_width = 15
    gantt.config.drag_move = false
    gantt.config.drag_links = false
    gantt.config.drag_resize = false
    gantt.config.drag_progress = false
    gantt.config.cascade_delete = false

    gantt.config.scales = [
        {unit: 'month', step: 1, format: '%F'},
        {unit: 'day', step: 1, format: '%j'}
    ]

    gantt.plugins({
        marker: true,
        tooltip: true,
        multiselect: true,
        drag_timeline: true
    })

    gantt.templates.task_end_date = function(date) {
        return gantt.templates.task_date(new Date(date.valueOf() - 1))
    }

    gantt.templates.timeline_cell_class = function(task,date) {
        if(date.getDay()==0||date.getDay()==6) return 'weekend'
    }

    let dateToStr = gantt.date.date_to_str(gantt.config.task_date)
    let now = gantt.addMarker({
        css: 'today',
        start_date: new Date(),
        text: dateToStr(new Date())
    })

    setInterval(function() {
        let marker = gantt.getMarker(now)
        marker.start_date = new Date()
        marker.text = dateToStr(new Date())
        gantt.updateMarker(now)
    }, 1000*60*60)

    function sortByNameDate(a, b) {
        if (a.text>b.text) return 1
        if (a.text<b.text) return -1
        if (a.start_date>b.start_date) return 1
        if (a.start_date<b.start_date) return -1
        return 0
    }

    gantt.init('<?=$pluginName;?>')

    if (database.dsn === '<?= EmbeddedDriver::DSN ?>') {
        gantt.parse(database.gantt)
    } else {
        throw new Error('UNSUPPORTED DSN!')
        //gantt.load('..URL..')
    }

    gantt.sort(sortByNameDate)
    gantt.showDate(new Date())

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
    })

    dp.attachEvent('onAfterUpdate', function(id, action, tid, response){
        if(action === 'error'){
            console.warn(response)
            alert('Error: ' + response)
        }
        gantt.sort(sortByNameDate)
    })

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
        })
    }
</script>
