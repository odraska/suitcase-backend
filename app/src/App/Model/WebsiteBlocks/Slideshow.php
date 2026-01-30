<?php

namespace SLONline\App\Model\WebsiteBlocks;

/**
 * Slideshow Website Block
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class Slideshow extends Images
{
    public const string FOLDER = 'Slideshows';
    private static string $table_name = 'SlideshowWebsiteBlock';

    private static string $singular_name = 'Slideshow block';
    private static string $plural_name = 'Slideshow blocks';
}
