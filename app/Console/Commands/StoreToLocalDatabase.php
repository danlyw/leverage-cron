<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Product;

class StoreToLocalDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store products manually to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$disk  = 'public';
		$files = $this->getFilesFromStorage();
		$files = json_encode($files);
		$files = json_decode($files);

		if (($key = array_search('.gitignore', $files)) !== false) {
			unset($files[$key]);
		}
		
		if (count($files) > 0) {

			foreach ($files as $file) {

				$this->info('Reading ' . $file);
	
				$product 	= Storage::disk($disk)->get($file);
				$product 	= json_decode(json_encode($product), true);
				$product	= json_decode($product);
				$length 	= count($product->ProductDetails);
				$file		= 0;
				$i			= 0;
				$division	= [
					'Division'		=> $product->DivisionInfo->Division,
					'DivisionName'	=> $product->DivisionInfo->DivisionName
				];
				
				if (count($product->ProductDetails) > 0) {
					foreach ($product->ProductDetails as $productDetail) {
						if (!$this->isProductExists($productDetail->ProdNbr)) {
							Product::create($this->populateCreateDataModel($productDetail, $division));
						}
						if ($i == $length - 1) {
							$this->deleteFileFromStorage($file);
						}
						$i++;
					}
				}
			}
		}
	}
	
	private function getFilesFromStorage()
	{
		$disk = 'public';

		return Storage::disk($disk)->allFiles('.');
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
