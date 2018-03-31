<?php
/**
 * Created by PhpStorm.
 * User: pottenwalder
 * Date: 2/19/2018
 * Time: 3:04 PM
 */

/*  =[
     'id_record',
     'created_on',
     'created_by',
     'active'
    ];
*/

/**
 * @schema general tables
 * @table_number 12
 */

$third_party = [
    'id_record ',
    'name',
    'last_name',
    'document',
    'document_type',
    'email',
    'phone',
    'born_date',
    'address_id',
    'created_on',
    'created_by',
    'active'
];

$address = [
    'id_record',
    'description',
    'city_id',
    'address_type',
    'created_on',
    'created_by',
    'active'
];

$address_type = [
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

$city = [
    'id_record',
    'description',
    'country_id',
    'created_on',
    'created_by',
    'active'
];

$country = [
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

$employee = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

$supplier = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

$customer = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

$document_type = [
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

$factories = [
    'id_record',
    'description',
    'address_id',
    'created_on',
    'created_by',
    'active'
];

$module = [
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

$factory_module = [
    'id_record',
    'module_id',
    'factory_id',
    'created_on',
    'created_by',
    'active'
];

/**
 * @schema corporate planning
 * @table_number 7
 */
$buy = [
    'id_record',
    'customer_id',
    'description',
    'comment',
    'receive_date',
    'created_on',
    'created_by',
    'active'
];

$buy_detail = [
    'id_record',
    'buy_id',
    'po_number',
    'product_id',
    'pre_pack',
    'oa_number',
    'created_on',
    'created_by',
    'active'
];

$mps = [
    'id_record',
    'buy_detail_id',
    'factory_id',
    'ex_factory_date',
    'contract',
    'publish',
    'deliver_date',
    'created_on',
    'created_by',
    'active'
];

$po_detail = [
    'id_record',
    'mps_id',
    'size',
    'pairs_qty',
    'box_pack_qty',
    'box_qty',
    'created_on',
    'created_by',
    'active'
];

$cutting_week = [
    'id_record',
    'week_id',
    'description',
    'start_date',
    'end_date',
    'end_date_production',
    'created_on',
    'created_by',
    'active'
];

$point = [
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

$operation = [
    'id_record',
    'description',
    'short_name',
    'point_id',
    'operation_type',
    'created_on',
    'created_by',
    'active'
];

/**
 * @schema purchase
 * @table_number 4
 */
$material_requarment = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

$material_requarment_detail = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

$purchase_order = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

$purchase_order_detail = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

/**
 * @schema warehouse
 * @table_number 8
 */
$warehouse = [
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

$warehouse_location = [
    'id_record',
    'warehouse_id',
    'description',
    'created_on',
    'created_by',
    'active'
];

$location_section = [
    'id_record',
    'location_id',
    'description',
    'created_on',
    'created_by',
    'active'
];

$section_anaquel = [
    'id_record',
    'section_id',
    'description',
    'created_on',
    'created_by',
    'active'
];

$anaquel_division = [
    'id_record',
    'anaquel_id',
    'description',
    'created_on',
    'created_by',
    'active'
];

$material_reception = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

$factory_distribution = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

/**
 * @schema product development
 * @table_number 5
 */
$product = [
    'id_record',
    'description',
    'customer_id',
    'created_on',
    'created_by',
    'active'
];

$product_detail = [
    'id_record',
    'product_id',
    'stock',
    'gender_id',
    'material_id',
    'color_id',
    'width_id',
    'created_on',
    'created_by',
    'active'
];

$color = [
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

$gender = [
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

$width = [
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

$material  =[
    'id_record',
    'description',
    'created_on',
    'created_by',
    'active'
];

/**
 * @schema production
 * @table_number 8
 */
$general_tickets = [
    'id_record',
    'ticket_id',
    'cutting_id',
    'created_on',
    'created_by',
    'active'
];

$product_standard = [
    'id_record',
    'factory_id',
    'module_id',
    'product_id',
    'operation_id',
    'created_on',
    'created_by',
    'active'
];

$planning_production = [
    'id_record',
    'factory_id',
    'module_id',
    'ticket_id',
    'cutting_week_id',
    'created_on',
    'created_by',
    'active'
];

$ticket_activities = [
    'id_record',
    'factory_id',
    'module_id',
    'ticket_id',
    'operation_id',
    'completed',
    'created_on',
    'created_by',
    'active'
];

$ticket_status = [
    'id_record',
    'factory_id',
    'module_id',
    'ticket_id',
    'operation_id',
    'completed',
    'created_on',
    'created_by',
    'active'
];

$license = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

$license_ticket = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];

$shipping_activities = [
    'id_record',
    'created_on',
    'created_by',
    'active'
];