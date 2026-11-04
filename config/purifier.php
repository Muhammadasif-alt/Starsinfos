<?php

return [
    'encoding' => 'UTF-8',
    'finalize' => true,
    'ignoreNonStrings' => false,
    'cachePath' => storage_path('app/purifier'),
    'cacheFileMode' => 0755,
    'settings' => [
        'default' => [
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => 'h2,h3,h4,p,br,hr,b,strong,i,em,u,s,strike,blockquote,pre,code,ul,ol,li,a[href|title|target|rel|class],img[src|alt|title|width|height],table,thead,tbody,tr,th,td',
            'Attr.AllowedFrameTargets' => ['_blank'],
            'Attr.AllowedClasses' => ['apply-button'],
            'AutoFormat.RemoveEmpty' => true,
        ],
    ],
];
