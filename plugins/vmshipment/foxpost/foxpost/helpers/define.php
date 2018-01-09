<?php
defined('JPATH_BASE') or die();
if (JVM_VERSION === 2) {
    if (!defined('JPATH_VMFOXPOSTPLUGIN'))
define('JPATH_VMFOXPOSTPLUGIN', JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment' . DS . 'foxpost');
} else {
if (!defined('JPATH_VMFOXPOSTPLUGIN'))
define('JPATH_VMFOXPOSTPLUGIN', JPATH_ROOT . DS . 'plugins' . DS . 'vmshipment');
}