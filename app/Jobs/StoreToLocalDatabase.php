<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Product;

class StoreToLocalDatabase implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	
	protected $data;

	public $timeout = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		echo 'Reading ' . $this->data->fileName . PHP_EOL;
		
		$file = $this->getFileFromStorage($this->data->fileName);
		
		if ($file) {

			$productData	= json_decode(json_encode($file), true);
			$productData	= json_decode($productData);
			$length 		= count($productData->ProductDetails);
			$file			= 0;
			$i				= 0;
			$division		= [
				'Division'		=> $productData->DivisionInfo->Division,
				'DivisionName'	=> $productData->DivisionInfo->DivisionName
			];
			
			foreach ($productData->ProductDetails as $ProductDetail) {
				if (!$this->isProductExists($ProductDetail->ProdNbr)) {
					Product::create($this->populateCreateDataModel($ProductDetail, $division));
				}
				if ($i == $length - 1) {
					$this->deleteFileFromStorage($this->data->fileName);
				}
				$i++;
			}
		}
	}
	
	private function getFileFromStorage($fileName)
	{
		$disk = 'public';

		if (Storage::disk($disk)->exists($fileName)) {
			return Storage::disk($disk)->get($fileName);
		}
		return null;
	}

	private function deleteFileFromStorage($fileName)
	{
		$disk = 'public';

		if (Storage::disk($disk)->exists($fileName)) {
			return Storage::disk($disk)->delete($fileName);
		}
	}

	private function isProductExists($productNumber)
	{
		$product = Product::where(['product_number' => $productNumber])->first();

		return $product;
	}

	private function populateCreateDataModel($product, $division)
	{
		return [
			'division'						=> $this->convertEmptyObjectIntoString($division['Division']),
			'division_name'					=> $this->convertEmptyObjectIntoString($division['DivisionName']),
			'product_number'				=> $this->convertEmptyObjectIntoString($product->ProdNbr),
			'description'					=> $this->convertEmptyObjectIntoString($product->Description),
			'status'						=> $this->convertEmptyObjectIntoString($product->Status),
			'brand_name'					=> $this->convertEmptyObjectIntoString($product->BrandName),
			'sls_pack_size'					=> $this->convertEmptyObjectIntoString($product->SlsPackSize),
			'sls_uom'						=> $this->convertEmptyObjectIntoString($product->SlsUOM),
			'prc_uom'						=> $this->convertEmptyObjectIntoString($product->PrcUOM),
			'each_uom'						=> $this->convertEmptyObjectIntoString($product->EachUOM),
			'each_conv_fctr'				=> $this->convertEmptyObjectIntoString($product->EachConvFctr),
			'each_pack_size'				=> $this->convertEmptyObjectIntoString($product->EachPackSize),
			'rpl_prod_nbr'					=> $this->convertEmptyObjectIntoString($product->RplProdNbr),
			'grs_wght'						=> $this->convertEmptyObjectIntoString($product->GrsWght),
			'net_wght'						=> $this->convertEmptyObjectIntoString($product->NetWght),
			'will_brk_ind'					=> $this->convertEmptyObjectIntoString($product->WillBrkInd),
			'var_wght_ind'					=> $this->convertEmptyObjectIntoString($product->VarWghtInd),
			'manufacturer_product_number'	=> $this->convertEmptyObjectIntoString($product->ManufacturerProductNumber),
			'manufacturer_name'				=> $this->convertEmptyObjectIntoString($product->ManufacturerName),
			'kosher_ind'					=> $this->convertEmptyObjectIntoString($product->KosherInd),
			'pre_sold_ind'					=> $this->convertEmptyObjectIntoString($product->PreSoldInd),
			'local_src_ind'					=> $this->convertEmptyObjectIntoString($product->LocalSrcInd),
			'jit_ind'						=> $this->convertEmptyObjectIntoString($product->JITInd),
			'rte_ind'						=> $this->convertEmptyObjectIntoString($product->RTEInd),
			'gtin'							=> $this->convertEmptyObjectIntoString($product->GTIN),
			'pim_class'						=> $this->convertEmptyObjectIntoString($product->PIMClass),
			'pim_catg'						=> $this->convertEmptyObjectIntoString($product->PIMCatg),
			'pim_grp'						=> $this->convertEmptyObjectIntoString($product->PIMGrp),
			'upc_case'						=> $this->convertEmptyObjectIntoString($product->UPCCase),
			'upc_pckg'						=> $this->convertEmptyObjectIntoString($product->UPCPckg)
		];
	}

	private function convertEmptyObjectIntoString($object)
	{
		$string = current((Array)$object);
		return trim(preg_replace('/\s\s+/', ' ', $string));
	}
}
