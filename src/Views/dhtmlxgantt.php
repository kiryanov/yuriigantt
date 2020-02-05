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

use \dokuwiki\plugin\yuriigantt\src\Driver\Embedded as EmbeddedDriver;

$withTranslation = function () use ($pluginName, $lang, $baseUrl) {
    $langMap = [
        'uk' => 'ua',
    ];
    $lang = preg_replace("/[^a-z]+/", "", $lang);

    if (in_array($lang, array_keys($langMap))) {
        $lang = $langMap[$lang];
    }

    $langFile = dirname(__DIR__, 2) . "/3rd/dhtmlxgantt/locale/locale_$lang.js";
    $langUrl = $baseUrl . "lib/plugins/{$pluginName}/3rd/dhtmlxgantt/locale/locale_$lang.js?v=6.3.5";

    if (!file_exists($langFile)) {
        return;
    }

    echo "<script src=\"$langUrl\"></script>";
};
?>
<link rel="stylesheet" href="<?= $baseUrl ?>lib/plugins/<?= $pluginName; ?>/3rd/dhtmlxgantt/dhtmlxgantt.css?v=6.3.5">
<style>
    .gantt-fullscreen {
        position: absolute;
        bottom: 10px;
        right: 10px;
        padding: 2px;
        background: transparent;
        cursor: pointer;
        opacity: 0.5;
        -webkit-transition: background-color 0.5s, opacity 0.5s;
        transition: background-color 0.5s, opacity 0.5s;
    }
    .gantt-fullscreen:hover {
        opacity: 1;
    }
</style>
<script src="<?= $baseUrl ?>lib/plugins/<?= $pluginName; ?>/3rd/dhtmlxgantt/dhtmlxgantt.js?v=6.3.5"></script>
<script src="<?= $baseUrl ?>lib/plugins/<?= $pluginName; ?>/3rd/dhtmlxgantt/ext/dhtmlxgantt_fullscreen.js?v=6.3.5"></script>
<?php $withTranslation(); ?>
<div id="<?= $pluginName; ?>"></div>
<script>
    let database = <?= json_encode($database); ?>;

    gantt.config.autosize = "y"
    gantt.config.date_format = "%d-%m-%Y %H:%i"
    gantt.config.order_branch = true
    gantt.config.order_branch_free = true

    // fullscreen -->
    gantt.attachEvent("onTemplatesReady", function () {
        var toggleIcon = document.createElement("img");
        toggleIcon.src = '<?= $baseUrl ?>lib/plugins/<?= $pluginName; ?>/3rd/fontawesome/expand-solid.svg';
        toggleIcon.className = 'gantt-fullscreen'
        toggleIcon.style.width = '20px'
        toggleIcon.style.height = '20px'
        gantt.toggleIcon = toggleIcon;
        gantt.$container.appendChild(toggleIcon);
        console.log(toggleIcon)
        toggleIcon.onclick = function() {
            gantt.ext.fullscreen.toggle();
        };
    });
    gantt.attachEvent("onExpand", function () {
        var toggleIcon = gantt.toggleIcon;
        //console.log(toggleIcon)
        if (toggleIcon) {
            toggleIcon.src = '<?= $baseUrl ?>lib/plugins/<?= $pluginName; ?>/3rd/fontawesome/compress-solid.svg';
        }

    });
    gantt.attachEvent("onCollapse", function () {
        var toggleIcon = gantt.toggleIcon;
        console.log(toggleIcon)
        if (toggleIcon) {
            toggleIcon.src = '<?= $baseUrl ?>lib/plugins/<?= $pluginName; ?>/3rd/fontawesome/expand-solid.svg';
        }
    });
    // <---

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
