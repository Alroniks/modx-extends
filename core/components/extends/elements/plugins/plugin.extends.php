<?php
/*
 * Nested templates in MODX Revolution
 *
 * Using: Make layout template and call it in other template as [[>layout]]
 * Use [[@yield]] for determine block that will be replaced by child temlate
 *
 * Required OnParseDocument event
 *
 * @author Ivan Klimchuk <ivan@klimchuk.com>
 */

if (!function_exists('ext')) {
    function ext($content, $currentTpl) {
        global $modx;
        $pattern = '#\[\[\>(.+?)\]\]#si';
        if (preg_match($pattern, $content, $m) > 0) {
            $tag = $m[0];
            $tpl = $m[1];

            $parentTpl = $modx->getObject('modTemplate', array('templatename' => $tpl));
            $currentTpl = $modx->getObject('modTemplate', $currentTpl);

            $content = $parentTpl->content;

            if (preg_match($pattern, $parentTpl->content)) {
                $content = ext($parentTpl->content, $parentTpl->id);
            }

            $currentTpl = str_replace($tag, '', $currentTpl->content);

            $content = str_replace(
                '[[@yield]]',
                $currentTpl,
                $content
            );
        }

        return $content;
    }
}

switch($modx->event->name) {
    case 'OnParseDocument':
        if ($modx->context->key != 'mgr') {
            $modx->documentOutput = ext($modx->documentOutput, $modx->resource->template);
        }
        break;
}