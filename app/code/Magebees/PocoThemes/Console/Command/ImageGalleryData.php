<?php
namespace Magebees\PocoThemes\Console\Command;

class ImageGalleryData
{
	public static function getImagegallery($first_store_id){
		$magebees_imagegallery = [
			['image_id' => '1','title' => 'Image Gallery 1','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '/#','sort_order' => '1','image' => '/i/n/instagram_img-1.jpg'],
			['image_id' => '2','title' => 'Image Gallery 2','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '/#','sort_order' => '2','image' => '/i/n/instagram_img-2.jpg'],
			['image_id' => '3','title' => 'Image Gallery 3','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '/#','sort_order' => '3','image' => '/i/n/instagram_img-3.jpg'],
			['image_id' => '4','title' => 'Image Gallery 4','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '/#','sort_order' => '4','image' => '/i/n/instagram_img-4.jpg'],
			['image_id' => '5','title' => 'Image Gallery 5','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '/#','sort_order' => '5','image' => '/i/n/instagram_img-5.jpg'],
			['image_id' => '6','title' => 'Image Gallery 6','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '/#','sort_order' => '6','image' => '/i/n/instagram_img-6.jpg'],
			['image_id' => '7','title' => 'Image Gallery 7','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '/#','sort_order' => '7','image' => '/i/n/instagram_img-7.jpg'],
			['image_id' => '8','title' => 'Image Gallery 7','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '/#','sort_order' => '8','image' => '/i/n/instagram_img-8.jpg'],
			['image_id' => '9','title' => 'Image Gallery 9','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '/#','sort_order' => '9','image' => '/i/n/instagram_img-9.jpg'],
			['image_id' => '10','title' => 'Image Gallery 10','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '10','image' => '/i/n/instagram_img-10.jpg'],
			['image_id' => '11','title' => 'Kids Gallery 1','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '1','image' => '/i/n/instagram_img-11.jpg'],
			['image_id' => '12','title' => 'Kids Gallery 2','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '2','image' => '/i/n/instagram_img-12.jpg'],
			['image_id' => '13','title' => 'Kids Gallery 3','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '3','image' => '/i/n/instagram_img-13.jpg'],
			['image_id' => '14','title' => 'Kids Gallery 4','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '4','image' => '/i/n/instagram_img-14.jpg'],
			['image_id' => '15','title' => 'Kids Gallery 5','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '5','image' => '/i/n/instagram_img-15.jpg'],
			['image_id' => '16','title' => 'Kids Gallery 6','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '6','image' => '/i/n/instagram_img-16.jpg'],
			['image_id' => '17','title' => 'Decor Gallery 1','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '1','image' => '/i/n/instagram_img-17.jpg'],
			['image_id' => '18','title' => 'Decor Gallery 2','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '2','image' => '/i/n/instagram_img-18.jpg'],
			['image_id' => '19','title' => 'Decor Gallery 3','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '3','image' => '/i/n/instagram_img-19.jpg'],
			['image_id' => '20','title' => 'Decor Gallery 4','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '4','image' => '/i/n/instagram_img-20.jpg'],
			['image_id' => '21','title' => 'Decor Gallery 5','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '5','image' => '/i/n/instagram_img-21.jpg'],
			['image_id' => '22','title' => 'Decor Gallery 6','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '6','image' => '/i/n/instagram_img-22.jpg'],
			['image_id' => '23','title' => 'Decor Gallery7','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '7','image' => '/i/n/instagram_img-23.jpg'],
			['image_id' => '24','title' => 'Decore Gallery8','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '8','image' => '/i/n/instagram_img-24.jpg'],
			['image_id' => '25','title' => 'Jewellery Gallery 1','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '0','image' => '/i/n/instagram_img-25.jpg'],
			['image_id' => '26','title' => 'Jewellery Gallery 2','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '1','image' => '/i/n/instagram_img-26.jpg'],
			['image_id' => '27','title' => 'Jewellery Gallery 3','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '2','image' => '/i/n/instagram_img-27.jpg'],
			['image_id' => '28','title' => 'Jewellery Gallery 4','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '3','image' => '/i/n/instagram_img-28.jpg'],
			['image_id' => '29','title' => 'Jewellery Gallery 5','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '4','image' => '/i/n/instagram_img-29.jpg'],
			['image_id' => '30','title' => 'Jewellery Gallery 6','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '5','image' => '/i/n/instagram_img-30.jpg'],
			['image_id' => '31','title' => 'Jewellery Gallery 7','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '6','image' => '/i/n/instagram_img-31.jpg'],
			['image_id' => '32','title' => 'Jewellery Gallery 8','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '7','image' => '/i/n/instagram_img-32.jpg'],
			['image_id' => '33','title' => 'Jewellery Gallery 9','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '8','image' => '/i/n/instagram_img-33.jpg'],
			['image_id' => '34','title' => 'Jewellery Gallery 10','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '9','image' => '/i/n/instagram_img-34.jpg'],
			['image_id' => '35','title' => 'Jewellery Gallery 11','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '10','image' => '/i/n/instagram_img-35.jpg'],
			['image_id' => '36','title' => 'Jewellery Gallery 12','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '11','image' => '/i/n/instagram_img-36.jpg'],
			['image_id' => '37','title' => 'Jewellery Gallery 13','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '12','image' => '/i/n/instagram_img-37.jpg'],
			['image_id' => '38','title' => 'Jewellery Gallery 14','stores' => $first_store_id,'status' => '1','isexternal' => '0','url' => '#','sort_order' => '13','image' => '/i/n/instagram_img-38.jpg']
		];
		return $magebees_imagegallery;
			
	}
}