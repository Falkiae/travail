<?php
require_once __DIR__ . '/_block_helpers.php';
echo isset($block['html']) ? $block['html'] : (isset($block['content']) ? $block['content'] : '');
?>
