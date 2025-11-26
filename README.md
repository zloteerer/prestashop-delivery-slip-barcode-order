Delivery Slip Order Barcode
===========================

What it does
------------
Adds a Code128 barcode to order delivery slips (PDF). The module generates a PNG barcode for the order reference and replace the header template so the barcode appears on the slip.

Compatibility
-------------
Designed for 8 (uses `displayPDFDeliverySlip` hook). No overrides. It uses module hook/template approach.

Install & Test
--------------
- Install the module via PrestaShop modules manager (or copy into `modules/`).
- Generate a delivery slip in the Back Office and check `img/tmp/barcode_<order_reference>.png` is created and visible in the PDF.

Notes
-----
- The module uses a generator class to create the PNG in `img/tmp/` and assigns `barcode_url` to the PDF template.
- If the PDF does not show the barcode, verify the `displayPDFDeliverySlip` hook is executed by your store theme or customizations.
