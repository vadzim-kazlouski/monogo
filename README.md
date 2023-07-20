# Monogo attribute module
1. Before using this module please configure all settins and provide credensials to QR code service in **Stores** -> **Configuration** - **Monogo**
2. Use console command `bin/magento monogo:synchronize-attribute:run [<product_count>]` to create message queue for product attribute update. Product attribute has a code `monogo`
3. To execute the queue and update the attribute run command `bin/magento queue:consumers:start MonogoAttributeSync`
4. Generated QR codes stored in the **pub/media/qr_codes** folder
5. Products with updated attributes will display the QR code on the PDP.