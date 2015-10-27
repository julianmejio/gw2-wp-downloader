<?php

// Walpapper URL
$wpurl = 'https://www.guildwars2.com/es/media/wallpapers/';

/**
 * Resolution to download. accept any of this values:
 * 800x600, 1024x768, 1280x960, 1280x1024, 1600x1200, 1280x720, 1280x800,
 * 1440x900, 1680x1050, 1920x1080, 1920x1200
 */
$wpres = '1920x1200';

// Handlers to manage data to download (the wallpapers)
$wprhandler = null;
$wpwhandler = null;

// Creation of the wallpaper directory
$wpdir = __DIR__ . '/wps';
if(!is_dir($wpdir) && !@mkdir($wpdir)) {
    echo "Wallpaper directory cannot be created" . PHP_EOL;
    return -1;
}

// Reading the wallpapper URL
$wpdom = new DOMDocument();
if(!@$wpdom->loadHTMLFile($wpurl)) {
    echo "Wallpaper URL cannot be readed" . PHP_EOL;
    return -1;
}
// And create an associated xpath obj
$wpxpath = new DOMXPath($wpdom);

// Query the img objects
$xresult = $wpxpath->query(sprintf(
        '//*/li[contains(@class, "wallpaper")]/ul/li/a[text()="%s"]', $wpres
));

// Download the wallpapers
for($i = 0; $i < $xresult->length; $i++) {

    // Retrieving the wallpaper URL, or conitnue if cannot retrieve.
    $xitemurl = $xresult->item($i)->attributes['href']->value;
    if(!preg_match(
        '/\\/([^\\/]+\.jpg)$/', $xitemurl, $taxonomy
    )) {
        continue;
    }

    // Info to user
    printf(
        "[%02u/%02u] Downloading %s... ", $i + 1, $xresult->length, $taxonomy[1]
    );

    // Creating the remote handler, or continue if cannot create.
    $file = sprintf("%s/%s", $wpdir, $taxonomy[1]);
    if(!$wprhandler = @fopen($xitemurl, "rb")) {
        echo "error" . PHP_EOL;
        continue;
    }

    // Creating the local handler, or conitnue if cannot create
    if(!$wpwhandler = @fopen($file, "xb")) {
        @fclose($wprhandler);
        echo "error" . PHP_EOL;
        continue;
    }

    // Writing data to local file
    while(!feof($wprhandler)) {
        @fwrite($wpwhandler, @fread($wprhandler, 8192));
    }
    // And close handlers
    @fclose($wprhandler);
    @fclose($wpwhandler);
    // ok
    echo "ok" . PHP_EOL;

}