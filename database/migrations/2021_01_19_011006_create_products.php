<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('division')->nullable();
			$table->string('division_name')->nullable();
			$table->integer('product_number')->nullable();
			$table->string('description')->nullable();
			$table->string('status')->nullable();
			$table->string('brand_name')->nullable();
			$table->string('sls_pack_size')->nullable();
			$table->string('sls_uom')->nullable();
			$table->string('prc_uom')->nullable();
			$table->string('each_uom')->nullable();
			$table->string('each_conv_fctr')->nullable();
			$table->string('each_pack_size')->nullable();
			$table->string('rpl_prod_nbr')->nullable();
			$table->string('grs_wght')->nullable();
			$table->string('net_wght')->nullable();
			$table->string('will_brk_ind')->nullable();
			$table->string('var_wght_ind')->nullable();
			$table->string('manufacturer_product_number')->nullable();
			$table->string('manufacturer_name')->nullable();
			$table->string('kosher_ind')->nullable();
			$table->string('pre_sold_ind')->nullable();
			$table->string('local_src_ind')->nullable();
			$table->string('jit_ind')->nullable();
			$table->string('rte_ind')->nullable();
			$table->string('gtin')->nullable();
			$table->string('pim_class')->nullable();
			$table->string('pim_catg')->nullable();
			$table->string('pim_grp')->nullable();
			$table->string('upc_case')->nullable();
			$table->string('upc_pckg')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
