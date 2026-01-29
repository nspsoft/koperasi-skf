<?php
$content = file_get_contents($argv[1]);
$tags = ['div', 'form', 'section', 'main'];
foreach ($tags as $tag) {
    $open = substr_count($content, "<$tag");
    $close = substr_count($content, "</$tag>");
    echo "$tag: Open=$open, Close=$close\n";
}
