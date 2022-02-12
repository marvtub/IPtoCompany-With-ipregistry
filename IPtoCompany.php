<?php 
/**
 * Plugin Name: IPtoCompany (Manipulated Matomo Plugin)
 * Plugin URI: http://plugins.matomo.org/IPtoCompany
 * Description: Get the name of the companies that visit your website.
 * Author: Marvin Aziz
 * Author URI: https://github.com/Romain/Matomo-IP-to-Company
 * Version: 1.0.2
 */
?><?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\IPtoCompany;

use Piwik\Db;
use Piwik\Piwik;
use Piwik\Common;
use Piwik\Plugin;
use Piwik\SettingsPiwik;
use Piwik\Widget\WidgetsList;
use \Exception;

 
if (defined( 'ABSPATH')
&& function_exists('add_action')) {
    $path = '/matomo/app/core/Plugin.php';
    if (defined('WP_PLUGIN_DIR') && WP_PLUGIN_DIR && file_exists(WP_PLUGIN_DIR . $path)) {
        require_once WP_PLUGIN_DIR . $path;
    } elseif (defined('WPMU_PLUGIN_DIR') && WPMU_PLUGIN_DIR && file_exists(WPMU_PLUGIN_DIR . $path)) {
        require_once WPMU_PLUGIN_DIR . $path;
    } else {
        return;
    }
    add_action('plugins_loaded', function () {
        if (function_exists('matomo_add_plugin')) {
            matomo_add_plugin(__DIR__, __FILE__, true);
        }
    });
}

class IPtoCompany extends \Piwik\Plugin
{
    /**
     * @see https://developer.matomo.org/guides/extending-database
     */
    public function install()
    {
        try {
            $sql = "CREATE TABLE " . Common::prefixTable('ip_to_company') . " (
                        id INTEGER NOT NULL AUTO_INCREMENT,
                        ip VARCHAR( 15 ) NOT NULL ,
                        as_number VARCHAR( 10 ) NULL ,
                        as_name VARCHAR( 200 ) NULL ,
                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
                        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
                        PRIMARY KEY ( id )
                    )  DEFAULT CHARSET=utf8 ";
            Db::exec($sql);
        } catch (Exception $e) {
            // ignore error if table already exists (1050 code is for 'table already exists')
            if (!Db::get()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
    }

    /**
     * @see https://developer.matomo.org/guides/extending-database
     */
    public function activate()
    {
        try {
            $sql = "CREATE TABLE " . Common::prefixTable('ip_to_company') . " (
                        id INTEGER NOT NULL AUTO_INCREMENT,
                        ip VARCHAR( 15 ) NOT NULL ,
                        as_number VARCHAR( 10 ) NULL ,
                        as_name VARCHAR( 200 ) NULL ,
                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
                        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
                        PRIMARY KEY ( id )
                    )  DEFAULT CHARSET=utf8 ";
            Db::exec($sql);
        } catch (Exception $e) {
            // ignore error if table already exists (1050 code is for 'table already exists')
            if (!Db::get()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
    }

    /**
     * @see https://developer.matomo.org/guides/extending-database
     */
    public function uninstall()
    {
        Db::dropTables(Common::prefixTable('ip_to_company'));
    }

    /**
     * @see \Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            //'AssetManager.getJavaScriptFiles' => 'getJsFiles',
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'Widget.filterWidgets' => 'filterWidgets'
        );
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = "plugins/IPtoCompany/stylesheets/iptocompany.less";
    }

    /*public function getJsFiles(&$jsFiles)
    {
        $jsFiles[] = "libs/bower_components/iframe-resizer/js/iframeResizer.min.js";

        $jsFiles[] = "plugins/Marketplace/angularjs/plugins/plugin-name.directive.js";
        $jsFiles[] = "plugins/Marketplace/angularjs/licensekey/licensekey.controller.js";
        $jsFiles[] = "plugins/Marketplace/angularjs/marketplace/marketplace.controller.js";
        $jsFiles[] = "plugins/Marketplace/angularjs/marketplace/marketplace.directive.js";
    }*/

    /**
     * @param WidgetsList $list
     */
    public function filterWidgets($list)
    {
        if (!SettingsPiwik::isInternetEnabled()) {
            $list->remove('IPtoCompany_Companies');
        }
    }
}
