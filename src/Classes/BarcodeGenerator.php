<?php

use TCPDFBarcode;

class BarcodeGenerator
{
    /**
     * Generate a barcode PNG file in img/tmp and return the filename (not full path).
     * Returns false on failure.
     *
     * @param string $value
     * @return string|false
     */
    public function generateBarcodeFile($value)
    {
        if (empty($value)) {
            return false;
        }

        try {
            $barcode = new TCPDFBarcode($value, 'C128');
            // width scale, height in user units, color
            $barcodeData = $barcode->getBarcodePngData(2, 50, [0, 0, 0]);

            // sanitize filename (keep ascii letters/numbers/_-)
            $safe = preg_replace('/[^A-Za-z0-9_\-]/', '_', $value);
            $filename = 'barcode_' . $safe . '.png';

            // Ensure tmp dir exists
            $tmpDir = _PS_TMP_IMG_DIR_;
            if (!is_dir($tmpDir)) {
                @mkdir($tmpDir, 0755, true);
            }

            $fullPath = rtrim($tmpDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
            file_put_contents($fullPath, $barcodeData);

            return $filename;
        } catch (Exception $e) {
            return false;
        }
    }
}
