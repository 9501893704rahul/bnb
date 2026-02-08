<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Typography\FontFactory;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImageTimestampService
{
    /**
     * Add timestamp overlay to image at bottom-right corner
     * Returns path to thumbnail with overlay (original preserved)
     */
    public static function overlayAndSave(string $storagePath, \DateTimeInterface $when): ?string
    {
        $absolutePath = Storage::disk('public')->path($storagePath);

        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            return null;
        }

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($absolutePath);

            // Calculate position for bottom-right corner with padding
            $x = $image->width() - 20;
            $y = $image->height() - 20;

            $text = $when->format('Y-m-d H:i:s');

            $image->text($text, $x, $y, function (FontFactory $font) {
                $font->size(28);
                $font->color('#ffffff');
                $font->align('right');
                $font->valign('bottom');
                $font->stroke('#000000', 2);
            });

            // Generate thumbnail path
            $pathInfo = pathinfo($storagePath);
            $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
            $thumbnailAbsPath = Storage::disk('public')->path($thumbnailPath);

            // Resize for web display (max 1200px width)
            $image->scale(width: 1200);
            $image->save($thumbnailAbsPath, 85);

            return $thumbnailPath;
        } catch (Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Legacy method for backward compatibility - overlays in place
     */
    public static function overlay(string $absolutePath, \DateTimeInterface $when): void
    {
        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            return;
        }

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($absolutePath);

            $x = $image->width() - 20;
            $y = $image->height() - 20;

            $text = $when->format('Y-m-d H:i:s');

            $image->text($text, $x, $y, function (FontFactory $font) {
                $font->size(28);
                $font->color('#ffffff');
                $font->align('right');
                $font->valign('bottom');
                $font->stroke('#000000', 2);
            });

            $image->save($absolutePath, 85);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
