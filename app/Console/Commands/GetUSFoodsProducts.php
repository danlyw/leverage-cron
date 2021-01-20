<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Jobs\StoreToLocalDatabase;

class GetUSFoodsProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usfoods:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Products from USFoods FTP';

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
		error_reporting(E_WARNING);

		$this->info('connecting to usfoods ftp server...');

		$this->getCatalogContents();

		$this->info('finished saving products.');
	}
	
	private function getCatalogContents()
	{
		$ftpHost   		= 'ediprod.usfoods.com';
		$ftpUsername 	= 'ECLEVBUY';
		$ftpPassword 	= 'ka0a1raP';
		$disk			= 'public';

		$connId 		= ftp_connect($ftpHost) or die("Couldn't connect to $ftpHost");
		$ftpLogin 		= ftp_login($connId, $ftpUsername, $ftpPassword);

		ftp_pasv($connId, true);

		$h 				= fopen('php://temp', 'r+');
		$contents 		= ftp_nlist($connId, "/CATALOG");

		foreach ($contents as $content) {

			$this->info($content.'.json');
			if (Storage::disk($disk)->exists($content.'.json')) {
				$this->info('Storing existing ' . $content);
				$this->storeExistingProfuctFileToDatabase($content.'.json');
			}
			if (Storage::disk($disk)->exists($content.'.json')) {
				continue;
			}

			$this->info('Downloading ' . $content);

			$h			= fopen('php://temp', 'r+');
			$result 	= ftp_fget($connId, $h, '/CATALOG/'.$content, FTP_BINARY, 0); 
			$fstats 	= fstat($h);
			fseek($h, 0);
			$data 		= fread($h, $fstats['size']);
			fclose($h);
			
			$product = $this->convertXMLToJson($data, $content);

			StoreToLocalDatabase::dispatch($product);
		}

		ftp_close($connId);
	}

	private function convertXMLToJson($xmlString, $fileName)
	{
		$xml = simplexml_load_string($xmlString);
		$json = json_encode($xml);

		Storage::disk('public')->put($fileName.'.json', $json);

		$product = json_decode($json);
		$product->fileName = $fileName;

		return $product;
	}

	private function storeExistingProfuctFileToDatabase($fileName)
	{
		$disk 				= 'public';
		$product 			= Storage::disk($disk)->get($fileName);
		$product			= json_decode($product);
		$product->fileName 	= $fileName;

		StoreToLocalDatabase::dispatch($product);
	}
}
