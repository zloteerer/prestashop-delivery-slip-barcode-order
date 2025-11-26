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
            && $this->registerHook('displayPDFDeliverySlip');
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
        $generator = new BarcodeGenerator();
        $barcodeFilename = $generator->generateBarcodeFile($reference);

        if ($barcodeFilename) {
            $barcodeUrl = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'img/tmp/' . $barcodeFilename;
            $smarty->assign(['barcode_url' => $barcodeUrl]);

            // Return rendered template HTML so the PDF generator includes it.
            // Using Module::display to render the module template with the assigned smarty vars.
            return $this->display(__FILE__, 'views/templates/hook/header.tpl');
        }
    }
}
