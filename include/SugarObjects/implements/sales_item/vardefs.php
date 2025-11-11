<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

$vardefs = [
    'fields' => [
        'asset_number' => [
            'name' => 'asset_number',
            'vname' => 'LBL_ASSET_NUMBER',
            'type' => 'varchar',
            'len' => 50,
            'comment' => 'Asset tag number of sales item in use',
        ],
        'book_value' => [
            'name' => 'book_value',
            'vname' => 'LBL_BOOK_VALUE',
            'type' => 'currency',
            'len' => '26,6',
            'comment' => 'Book value of sales item in use',
            'related_fields' => [
                'currency_id',
                'base_rate',
            ],
        ],
        'book_value_date' => [
            'name' => 'book_value_date',
            'vname' => 'LBL_BOOK_VALUE_DATE',
            'type' => 'date',
            'comment' => 'Date of book value for sales item in use',
        ],
        'date_purchased' => [
            'name' => 'date_purchased',
            'vname' => 'LBL_DATE_PURCHASED',
            'type' => 'date',
            'comment' => 'Date sales item purchased',
        ],
        'date_support_expires' => [
            'name' => 'date_support_expires',
            'vname' => 'LBL_DATE_SUPPORT_EXPIRES',
            'type' => 'date',
            'comment' => 'Support expiration date',
        ],
        'date_support_starts' => [
            'name' => 'date_support_starts',
            'vname' => 'LBL_DATE_SUPPORT_STARTS',
            'type' => 'date',
            'comment' => 'Support start date',
        ],
        'list_price' => [
            'name' => 'list_price',
            'vname' => 'LBL_LIST_PRICE',
            'type' => 'currency',
            'len' => '26,6',
            'audited' => true,
            'comment' => 'List price of sales item',
            'related_fields' => [
                'currency_id',
                'base_rate',
            ],
        ],
        'pricing_factor' => [
            'name' => 'pricing_factor',
            'vname' => 'LBL_PRICING_FACTOR',
            'type' => 'int',
            'group' => 'pricing_formula',
            'len' => 4,
            'comment' => 'Variable pricing factor depending on pricing_formula',
        ],
        'pricing_formula' => [
            'name' => 'pricing_formula',
            'vname' => 'LBL_PRICING_FORMULA',
            'type' => 'varchar',
            'len' => 100,
            'comment' => 'Pricing formula (ex: Fixed, Markup over Cost)',
        ],
        'quantity' => [
            'name' => 'quantity',
            'vname' => 'LBL_QUANTITY',
            'type' => 'decimal',
            'len' => 12,
            'precision' => 2,
            'comment' => 'Quantity in use',
            'default' => 1.0,
        ],
        'serial_number' => [
            'name' => 'serial_number',
            'vname' => 'LBL_SERIAL_NUMBER',
            'type' => 'varchar',
            'len' => 50,
            'comment' => 'Serial number of sales item in use',
        ],
        'renewable' => [
            'name' => 'renewable',
            'vname' => 'LBL_RENEWABLE',
            'type' => 'bool',
            'comment' => 'Indicates whether the sales item is renewable (e.g. a service)',
            'related_fields' => [
                'service_duration',
                'service_start_date',
                'service_end_date',
                'service',
            ],
        ],
        'service' => [
            'name' => 'service',
            'vname' => 'LBL_SERVICE',
            'type' => 'bool',
            'default' => 0,
            'comment' => 'Indicates whether the sales item is a service or a product',
            'related_fields' => [
                'renewable',
                'service_duration',
                'service_start_date',
                'service_end_date',
            ],
        ],
        'service_duration_value' => [
            'name' => 'service_duration_value',
            'vname' => 'LBL_SERVICE_DURATION_VALUE',
            'type' => 'int',
            'min' => '1',
            'len' => '5',
            'required' => false,
            'studio' => false,
            'massupdate' => false,
            'comment' => 'Value of the service duration, if service duration is 4 Months the value is 4',
        ],
        'service_duration_unit' => [
            'name' => 'service_duration_unit',
            'vname' => 'LBL_SERVICE_DURATION_UNIT',
            'type' => 'enum',
            'options' => 'service_duration_unit_dom',
            'len' => 50,
            'audited' => false,
            'studio' => false,
            'massupdate' => false,
            'comment' => 'Service Duration unit like Year(s), Month(s) or Day(s)',
        ],
        'catalog_service_duration_value' => [
            'name' => 'catalog_service_duration_value',
            'vname' => 'LBL_CATALOG_SERVICE_DURATION_VALUE',
            'type' => 'int',
            'min' => '1',
            'len' => '5',
            'required' => false,
            'studio' => false,
            'massupdate' => false,
            'readonly' => true,
            'related_fields' => [
                'service_duration_multiplier',
            ],
            'comment' => 'Stores a Product Catalog item\'s Service Duration Value, used for duration comparisons',
        ],
        'catalog_service_duration_unit' => [
            'name' => 'catalog_service_duration_unit',
            'vname' => 'LBL_CATALOG_SERVICE_DURATION_UNIT',
            'type' => 'enum',
            'options' => 'service_duration_unit_dom',
            'len' => 50,
            'audited' => false,
            'studio' => false,
            'massupdate' => false,
            'readonly' => true,
            'related_fields' => [
                'service_duration_multiplier',
            ],
            'comment' => 'Stores a Product Catalog item\'s Service Duration Unit, used for duration comparisons',
        ],
        'service_duration_multiplier' => [
            'name' => 'service_duration_multiplier',
            'vname' => 'LBL_SERVICE_DURATION_MULTIPLIER',
            'type' => 'decimal',
            'studio' => false,
            'calculated' => true,
            'enforced' => true,
            'formula' => '
            ifElse(
                and(
                    isNumeric($service_duration_value),
                    isNumeric($catalog_service_duration_value),
                    or(equal($service_duration_unit, "year"), equal($service_duration_unit, "month"), equal($service_duration_unit, "day")),
                    or(equal($catalog_service_duration_unit, "year"), equal($catalog_service_duration_unit, "month"), equal($catalog_service_duration_unit, "day"))
                ),
                divide(
                    ifElse(
                        equal($service_duration_unit, "year"),
                        multiply($service_duration_value, 365),
                        ifElse(
                            equal($service_duration_unit, "month"),
                            multiply($service_duration_value, divide(365,12)),
                            ifElse(
                                equal($service_duration_unit, "day"),
                                $service_duration_value,
                                ""
                            )
                        )
                    ),
                    ifElse(
                        equal($catalog_service_duration_unit, "year"),
                        multiply($catalog_service_duration_value, 365),
                        ifElse(
                            equal($catalog_service_duration_unit, "month"),
                            multiply($catalog_service_duration_value, divide(365,12)),
                            ifElse(
                                equal($catalog_service_duration_unit, "day"),
                                $catalog_service_duration_value,
                                ""
                            )
                        )
                    )
                ),
                1
            )',
            'related_fields' => [
                'catalog_service_duration_value',
                'catalog_service_duration_unit',
            ],
            'comment' => 'Stores a multiplier based on the ratio of this sales item\'s duration to another duration (such as a Product Template\'s)',
        ],
        'service_end_date' => [
            'name' => 'service_end_date',
            'vname' => 'LBL_SERVICE_END_DATE',
            'type' => 'service-enddate',
            'dbType' => 'date',
            'comment' => 'End date of the service',
            'related_fields' => [
                'service_duration',
                'service_start_date',
                'renewable',
                'service',
            ],
        ],
        'service_start_date' => [
            'name' => 'service_start_date',
            'vname' => 'LBL_SERVICE_START_DATE',
            'type' => 'date',
            'comment' => 'Start date of the service',
            'related_fields' => [
                'service_duration',
                'service_end_date',
                'renewable',
                'service',
            ],
            'validation' => [
                'type' => 'isbefore',
                'compareto' => 'service_end_date',
                'datatype' => 'date',
            ],
        ],
        'support_contact' => [
            'name' => 'support_contact',
            'vname' => 'LBL_SUPPORT_CONTACT',
            'type' => 'varchar',
            'len' => 50,
            'comment' => 'Contact for support purposes',
        ],
        'support_description' => [
            'name' => 'support_description',
            'vname' => 'LBL_SUPPORT_DESCRIPTION',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'Description of sales item for support purposes',
        ],
        'support_name' => [
            'name' => 'support_name',
            'vname' => 'LBL_SUPPORT_NAME',
            'type' => 'varchar',
            'len' => 50,
            'comment' => 'Name of sales item for support purposes',
        ],
        'support_term' => [
            'name' => 'support_term',
            'vname' => 'LBL_SUPPORT_TERM',
            'type' => 'varchar',
            'len' => 100,
            'comment' => 'Term (length) of support contract',
        ],
        'tax_class' => [
            'name' => 'tax_class',
            'vname' => 'LBL_TAX_CLASS',
            'type' => 'enum',
            'options' => 'tax_class_dom',
            'len' => 100,
            'comment' => 'Tax classification (ex: Taxable, Non-taxable)',
            'default' => 'Taxable',
        ],
        'vendor_part_num' => [
            'name' => 'vendor_part_num',
            'vname' => 'LBL_VENDOR_PART_NUM',
            'type' => 'varchar',
            'len' => 50,
            'comment' => 'Vendor part number',
        ],
        'website' => [
            'name' => 'website',
            'vname' => 'LBL_URL',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'Sales item URL',
        ],
        'weight' => [
            'name' => 'weight',
            'vname' => 'LBL_WEIGHT',
            'type' => 'decimal',
            'len' => '12,2',
            'precision' => 2,
            'comment' => 'Weight of the sales item',
        ],
        'renewal' => [
            'name' => 'renewal',
            'vname' => 'LBL_RENEWAL',
            'type' => 'bool',
            'default' => 0,
            'readonly' => true,
            'comment' => 'Indicates whether this line item is a renewal',
        ],
    ],
    'uses' => [
        'currency',
    ],
];
