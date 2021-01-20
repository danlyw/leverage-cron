<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $connection = 'sqlite';

    protected $fillable = [
		'division',
		'division_name',
		'product_number',
		'description',
		'status',
		'brand_name',
		'sls_pack_size',
		'sls_uom',
		'prc_uom',
		'each_uom',
		'each_conv_fctr',
		'each_pack_size',
		'rpl_prod_nbr',
		'grs_wght',
		'net_wght',
		'will_brk_ind',
		'var_wght_ind',
		'manufacturer_product_number',
		'manufacturer_name',
		'kosher_ind',
		'pre_sold_ind',
		'local_src_ind',
		'jit_ind',
		'rte_ind',
		'gtin',
		'pim_class',
		'pim_catg',
		'pim_grp',
		'upc_case',
		'upc_pckg'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];
}
