<?php

namespace SLONline\App\TinyMCE;

use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\TinyMCE\TinyMCEConfig;

/**
 * Small Tiny MCE Config
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class SmallTinyMCEConfig extends TinyMCEConfig
{
    protected array $plugins = [
        'autolink' => null,
        'visualblocks' => null,
        'wordcount' => null,
        'code' => null,
    ];

    /**
     * Holder list of buttons, organised by line. This array is 1-based indexed array
     *
     * {@link https://www.tiny.cloud/docs/tinymce/6/basic-setup/#toolbar-configuration}
     *
     * @var array
     */
    protected $buttons = [
        1 => [
            'bold', 'italic', 'underline', 'removeformat', '|',
            'alignleft', 'aligncenter', 'alignright', 'alignjustify', '|','pastetext',

        ],
        2 => ['blocks','sslink', 'unlink', 'code', 'visualblocks'],
        3 => []
    ];

    public function __construct()
    {
        parent::__construct();

        $this->setOptions([
            'friendly_name' => 'Default CMS',
            'priority' => '50',
            'skin' => 'silverstripe',
            'contextmenu' => "searchreplace | sslink anchor ssmedia ssembed inserttable | cell row column deletetable",
            'use_native_selects' => false,
        ]);
        $this->insertButtonsAfter('table', 'anchor');

        // Prepare list of plugins to enable
        $moduleManifest = ModuleLoader::inst()->getManifest();
        $module = $moduleManifest->getModule('silverstripe/htmleditor-tinymce');
        $plugins = [];

        // Add link plugins if silverstripe/admin is installed.
        // The JS in these relies on some of the admin code e.g. modals.
        if ($moduleManifest->moduleExists('silverstripe/admin')) {
            $plugins += [
                'sslink' => $module->getResource('client/dist/js/TinyMCE_sslink.js'),
                'sslinkexternal' => $module->getResource('client/dist/js/TinyMCE_sslink-external.js'),
                'sslinkemail' => $module->getResource('client/dist/js/TinyMCE_sslink-email.js'),
            ];

        }

        // Add plugins for managing assets if silverstripe/asset-admin is installed
        if ($moduleManifest->moduleExists('silverstripe/asset-admin')) {
            $plugins += [
                'ssmedia' => $module->getResource('client/dist/js/TinyMCE_ssmedia.js'),
                'sslinkfile' => $module->getResource('client/dist/js/TinyMCE_sslink-file.js'),
            ];
        }

        // Add internal link plugins if silverstripe/cms is installed
        if ($moduleManifest->moduleExists('silverstripe/cms')) {
            $plugins += [
                'sslinkinternal' => $module->getResource('client/dist/js/TinyMCE_sslink-internal.js'),
                'sslinkanchor' => $module->getResource('client/dist/js/TinyMCE_sslink-anchor.js'),
            ];
        }

        if (!empty($plugins)) {
            $this->enablePlugins($plugins);
        }
    }
}
