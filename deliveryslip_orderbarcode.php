<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

// Use composer's autoload if available and include module src helpers
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class DeliverySlip_OrderBarcode extends Module
{
    public function __construct()
    {
        $this->name = 'deliveryslip_orderbarcode';
        $this->version = '1.0.0';
        $this->author = 'zloteerer';
        $this->tab = 'administration';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Delivery Slip - Order Barcode';
        $this->description = 'Add a barcode at the top of the delivery slip that is linked to the order reference.';
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayPDFDeliverySlip')
            && $this->installTemplate();
    }

    /**
     * Hook called just before rendering the delivery slip PDF
     */
    public function hookDisplayPDFDeliverySlip($hookArgs)
    {
        if (!isset($hookArgs['smarty']) || !isset($hookArgs['object'])) {
            return;
        }

        $smarty = $hookArgs['smarty'];
        $object = $hookArgs['object'];

        $order = new Order($object->id_order);
        if (!Validate::isLoadedObject($order)) {
            return;
        }

        $reference = $order->reference;

        // Generate barcode PNG in tmp and expose URL to the PDF template
        $generator = new OrderBarcodeGenerator();
        $barcodeFilename = $generator->generateBarcodeFile($reference);

        if ($barcodeFilename) {
            $barcodeUrl = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'img/tmp/' . $barcodeFilename;
            $smarty->assign(['barcode_url' => $barcodeUrl]);
        }
    }

        public function installTemplate()
    {
        $template_path = _PS_THEME_DIR_ . 'pdf';
        $template_file = _PS_MODULE_DIR_ . $this->name . '/views/pdf/header.tpl';
        $template_override_file = $template_path . '/header.tpl';

        if (!file_exists($template_path)) {
            @mkdir($template_path, 0755, true);
        }

        // Secure deletion of the old version
        if (file_exists($template_override_file)) {
            $this->safeUnlink($template_override_file, $template_path);
        }

        // Copy the new template
        @copy($template_file, $template_override_file);

        return true;
    }

    public function uninstallTemplate()
    {
        $template_path = _PS_THEME_DIR_ . 'pdf';
        $template_file = $template_path . '/header.tpl';

        $this->safeUnlink($template_file, $template_path);

        return true;
    }

    private function safeUnlink(string $target, string $baseDir): bool
    {
        $allowedBase = realpath($baseDir);
        if ($allowedBase === false) {
            return false;
        }

        // If relative path -> prepend base
        if (!preg_match('#^(?:/|[a-zA-Z]:\\\\)#', $target)) {
            $candidate = $allowedBase . DIRECTORY_SEPARATOR . ltrim($target, '/\\');
        } else {
            $candidate = $target;
        }

        // Resolve real path
        $real = realpath($candidate);
        if ($real === false) {
            $realDir = realpath(dirname($candidate));
            if ($realDir === false) {
                return false;
            }
            $real = $realDir . DIRECTORY_SEPARATOR . basename($candidate);
        }

        // Check that the file is in the allowed directory
        if (strpos($real, $allowedBase) !== 0) {
            return false;
        }

        // Check that it is a deletable file
        if (!is_file($real) || !is_writable($real)) {
            return false;
        }

        try {
            return unlink($real);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
