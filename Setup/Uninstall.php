<?php


namespace Vnecoms\VendorsPayment\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        //uninstall code, drop related tables
        $installer->getConnection()->dropTable($installer->getTable('vnecoms_vendor_payment'));

        $installer->endSetup();
    }
}
