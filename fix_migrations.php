<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$pendingMigrations = [
    '2025_11_06_101329_create_customer_to_products_table',
    '2025_11_06_101329_create_customers_table',
    '2025_11_06_101329_create_failed_jobs_table',
    '2025_11_06_101329_create_invoices_table',
    '2025_11_06_101329_create_notifications_table',
    '2025_11_06_101329_create_packages_table',
    '2025_11_06_101329_create_password_resets_table',
    '2025_11_06_101329_create_payments_table',
    '2025_11_06_101329_create_personal_access_tokens_table',
    '2025_11_06_101329_create_settings_table',
    '2025_11_06_101329_create_subscriptions_table',
    '2025_11_06_101329_create_system_settings_table',
    '2025_11_06_101329_create_users_table',
    '2025_11_06_101330_create_monthly_revenue_summary_view',
    '2025_11_06_101332_add_foreign_keys_to_customer_to_packages_table',
    '2025_11_06_101332_add_foreign_keys_to_customers_table',
    '2025_11_10_060151_create_sessions_table',
    '2025_11_10_082250_add_missing_columns_to_payments_table',
    '2025_11_10_082359_add_transaction_id_to_payments_table',
    '2025_11_10_082423_add_transaction_id_to_payments_table',
    '2025_11_11_080509_rename_packages_to_products',
    '2025_11_11_080538_rename_customer_to_packages_to_customer_to_products',
    '2025_11_11_080621_update_foreign_keys_in_customer_to_products',
    '2025_11_11_090000_create_product_types_table',
    '2025_11_11_090001_fix_product_type_id_column_in_products_table',
    '2025_11_11_090002_change_product_type_id_to_varchar_in_products_table',
    '2025_11_11_090003_rename_product_types_to_product_type_and_add_descriptions',
    '2025_11_11_090004_change_product_type_to_product_type_id_and_add_foreign_key',
    '2025_11_11_115949_fix_payments_foreign_keys',
    '2025_11_15_060245_add_invoice_id_to_customer_to_products_table',
    '2025_11_16_150000_update_customer_to_products_due_date',
    '2025_11_17_update_payments_table',
    '2025_11_19_095417_add_subtotal_to_customer_to_products_table',
    '2025_11_20_064900_add_customer_product_id_to_customer_to_products_table',
    '2025_11_20_100000_remove_subtotal_from_customer_to_products_table',
    '2025_11_20_110000_create_monthly_billing_summaries_table',
    '2025_11_23_100000_add_custom_price_to_customer_to_products_table',
    '2025_11_23_105828_fix_cp_id_auto_increment_in_customer_to_products_table',
    '2025_11_27_084707_add_missing_columns_to_invoices_table',
    '2025_11_30_150000_add_confirmed_status_to_invoices_table',
    '2025_12_04_110000_add_paused_status_to_customer_to_products_table',
    '2025_12_04_235736_add_deleted_at_to_customer_to_products_table',
    '2025_12_10_105831_update_existing_invoices_for_rolling',
    '2025_12_13_170703_add_admin_settings_to_system_settings_table',
    '2025_12_13_175052_add_admin_image_settings_to_system_settings_table'
];

foreach ($pendingMigrations as $migration) {
    try {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => 1
        ]);
        echo "Marked $migration as run\n";
    } catch (Exception $e) {
        echo "Failed to mark $migration: " . $e->getMessage() . "\n";
    }
}

echo "Done!\n";
