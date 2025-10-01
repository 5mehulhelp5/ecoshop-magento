<?php
namespace Magebees\PocoThemes\Console\Command;
use Magebees\PocoThemes\Helper\Data;
use Magento\Framework\App\State;
use Magento\Framework\App\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\ResourceConnection;
class SampleData extends Command
{
    /**     * @var State     */
    protected $appState;
	protected $resource;
	protected $connection;


    public function __construct(State $appState, ResourceConnection $resource)
    {
        $this->appState = $appState;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        parent::__construct("magebees:pocotheme:installsampledata");
    }
    /**     * Configure cli command.     */
    protected function configure(): void
    {
        $this->setName("magebees:pocotheme:installsampledata")->setDescription(
            "Run the installsampledata for install theme sample data."
        );
        parent::configure();
    }
    /**     * Execute cli command     * @param InputInterface $input     * @param OutputInterface $output     * @return $this|int|null     * @throws \Magento\Framework\Exception\LocalizedException     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exitCode = 0;
        $exitCode = 1;
        $this->appState->setAreaCode("frontend");
        /**         * @var $helper Data         */
        $output->writeln(
            "<info>Magebees Poco Themes Sample Data Install Start.</info>"
        );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $pocoBaseHelper = $objectManager->create(
            "Magebees\PocoBase\Helper\Data"
        );
        $helper = $objectManager->create("Magebees\PocoThemes\Helper\Data");
        $storeIds = $helper->getAllStoreIds();
		$blog_storeIds = $helper->getAllBlogStoreIds();
        $first_store_id = 1;
        if (isset($storeIds["store_ids_arr"][1])) {
            $first_store_id = $storeIds["store_ids_arr"][1];
        }
		$page_baanner_store_id = 1;
		if (isset($storeIds["store_ids_arr"][0])) {
            $page_baanner_store_id = $storeIds["store_ids_arr"][0];
        }
		if (isset($storeIds["store_ids_arr"][1])) {
            $first_store_id = $storeIds["store_ids_arr"][1];
        }
        $current_date_time = date("Y-m-d h:i:s");
        $time = strtotime($current_date_time);
        $current_date_time = date("Y-m-d h:i:s", strtotime("-5 day", $time));
        $currentdate = date("Y-m-d", strtotime("-5 day", $time));
        $to_date = date("Y-m-d h:i:s", strtotime("+1 month", $time));
        $baseUrl = $helper->getUrl();
        $customerGroupsIds = $helper->getCustomerGroupIds();
        $customerGroupsIds_arr = null;
        if ($customerGroupsIds) {
            $customerGroupsIds_arr = explode(",", $customerGroupsIds);
        }
		$about_us = $helper->getCMSPageId('about-us');
		$about_us_morden = $helper->getCMSPageId('about-modern');
		$faqs = $helper->getCMSPageId('faqs');
		$privacy_policy = $helper->getCMSPageId('privacy-policy');
		$payment_policy = $helper->getCMSPageId('payment-policy');
		$return_policy = $helper->getCMSPageId('return-policy');
		$term_condition = $helper->getCMSPageId('terms-condition');
		$page_404 = $helper->getCMSPageId('no-route');
		$find_store = $helper->getCMSPageId('find-a-store');


		$furniture_style = $helper->getCMSPageId('poco-themes-style-1');
		$fashion_style = $helper->getCMSPageId('poco-themes-style-2');
		$cosmetic_style = $helper->getCMSPageId('poco-themes-style-3');
		$electronic_style = $helper->getCMSPageId('poco-themes-style-4');
		$autoparts_style = $helper->getCMSPageId('poco-themes-style-5');
		$medical_style = $helper->getCMSPageId('poco-themes-style-6');
		$kids_style = $helper->getCMSPageId('poco-themes-style-7');
		$fashion2_style = $helper->getCMSPageId('poco-themes-style-8');
		$decor_style = $helper->getCMSPageId('poco-themes-style-9');
		$footwear_style = $helper->getCMSPageId('poco-themes-style-10');
		$jewellery_style = $helper->getCMSPageId('poco-themes-style-11');
		$vitamin_style = $helper->getCMSPageId('poco-themes-style-12');
		$gun_style = $helper->getCMSPageId('poco-themes-style-13');
		$gaming_style = $helper->getCMSPageId('poco-themes-style-14');
		$bakery_style = $helper->getCMSPageId('poco-themes-style-15');
		$wine_style = $helper->getCMSPageId('poco-themes-style-16');
		$megastore_style = $helper->getCMSPageId('poco-themes-style-17');
		$pet_style = $helper->getCMSPageId('poco-themes-style-18');
		$bike_style = $helper->getCMSPageId('poco-themes-style-19');

		$fashion_megamenu_banner = $helper->getStaticBlockId('fashion-megamenu-banner');
		$furniture_megamenu_banner = $helper->getStaticBlockId('furniture-megamenu-banner');
		$cosmetic_megamenu_banner = $helper->getStaticBlockId('cosmetic_megamenu_banner');
		$electronic_megamenu_banner = $helper->getStaticBlockId('electronic_megamenu_banner');
		$autoparts_megamenu_banner = $helper->getStaticBlockId('autoparts_megamenu_banner');
		$medical_megamenu_banner = $helper->getStaticBlockId('medical_megamenu_banner');
		$kids_megamenu_banner = $helper->getStaticBlockId('kids-megamenu-banner');
		$decor_megamenu_banner = $helper->getStaticBlockId('decor-megamenu-banner');
		$footwear_megamenu_banner = $helper->getStaticBlockId('footwear-megamenu-banner');
		$jewellery_megamenu_banner = $helper->getStaticBlockId('jewellery-megamenu-banner');
		$vitamin_megamenu_banner = $helper->getStaticBlockId('vitamin-megamenu-banner');
		$gun_megamenu_banner = $helper->getStaticBlockId('gun-megamenu-banner');
		$gaming_megamenu_banner = $helper->getStaticBlockId('gaming-megamenu-banner');
		$bakery_megamenu_banner = $helper->getStaticBlockId('bakery-shop-megamenu-banner');
		$wine_megamenu_banner = $helper->getStaticBlockId('wine-shop-megamenu-banner');
		$megastore_megamenu_banner = $helper->getStaticBlockId('mega-store-shop-mega-menu-banner');
		$pet_megamenu_banner = $helper->getStaticBlockId('pet-store-shop-mega-menu-banner');
		$bike_megamenu_banner = $helper->getStaticBlockId('bike-shop-megamenu-banner');

		/*  Advertisement block Start */
        $magebees_advertisement_information =
		// Call the static method from the AdvertisementData class
        $magebees_advertisement_information = AdvertisementData::getAdvertisementInformation();
		$magebees_advertisement_images = AdvertisementData::getAdvertisementImages();

        if (
            count($magebees_advertisement_information) > 0 &&
            count($magebees_advertisement_images) > 0
        ) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName(
                        "magebees_advertisement_information"
                    )
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName(
                        "magebees_advertisement_images"
                    )
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName(
                    "magebees_advertisement_information"
                ),
                $magebees_advertisement_information
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_advertisement_images"),
                $magebees_advertisement_images
            );
            $output->writeln(
                "<info>Magebees_Advertisementblock Sample Data Insterted Successfully.</info>"
            );
        }
        /*  Advertisement block Start */
        /* Blog Start */
        $magebees_blog_category = BlogData::getBlogCategory($blog_storeIds,$customerGroupsIds);
		$magebees_blog_comment = BlogData::getBlogComment($currentdate);
		$magebees_blog_tag = BlogData::getBlogTag();
        $magebees_blog_post = BlogData::getBlogPost($current_date_time,$customerGroupsIds,$storeIds);

		$magebees_blog_post_like_dislike = [
            [
                "like_dislike_id" => "1",
                "post_id" => "17",
                "store_id" => $storeIds["store_ids_str"],
                "customer_id" => $customerGroupsIds,
                "system_ip" => "27.56.182.48",
                "postlike" => "1",
                "postdislike" => "0",
            ],
        ];

		$magebees_blog_url_rewrite = BlogData::getBlogUrlRewrite();


        if (
            count($magebees_blog_category) > 0 &&
            count($magebees_blog_comment) > 0 &&
            count($magebees_blog_tag) > 0 &&
            count($magebees_blog_post) > 0 &&
            count($magebees_blog_post_like_dislike) > 0 &&
            count($magebees_blog_url_rewrite) > 0
        ) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_blog_category")
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_blog_comment")
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_blog_tag")
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_blog_post")
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName(
                        "magebees_blog_post_like_dislike"
                    )
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_blog_url_rewrite")
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_blog_category"),
                $magebees_blog_category
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_blog_comment"),
                $magebees_blog_comment
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_blog_tag"),
                $magebees_blog_tag
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_blog_post"),
                $magebees_blog_post
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName(
                    "magebees_blog_post_like_dislike"
                ),
                $magebees_blog_post_like_dislike
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_blog_url_rewrite"),
                $magebees_blog_url_rewrite
            );
            $output->writeln(
                "<info>Magebees_Blog Sample Data Insterted Successfully.</info>"
            );
        }
        /* Finder Start */ $magebees_finder = [
            [
                "finder_id" => "1",
                "title" => "Find Auto Parts",
                "number_of_dropdowns" => "3",
                "dropdown_style" => "horizontal",
                "no_of_columns" => "3",
                "category_ids" => "15,20,25,30",
                "status" => "1",
                "created_time" => $current_date_time,
                "update_time" => $current_date_time,
            ],
        ];
        $magebees_finder_dropdowns = [
            [
                "dropdown_id" => "1",
                "finder_id" => "1",
                "name" => "Select Year",
                "sort" => "asc",
            ],
            [
                "dropdown_id" => "2",
                "finder_id" => "1",
                "name" => "Select Make",
                "sort" => "asc",
            ],
            [
                "dropdown_id" => "3",
                "finder_id" => "1",
                "name" => "Select Model",
                "sort" => "asc",
            ],
        ];
        $magebees_finder_ymm_value = [
            [
                "ymm_value_id" => "4",
                "dropdown_id" => "1",
                "parent_id" => "0",
                "value" => "2022",
            ],
            [
                "ymm_value_id" => "5",
                "dropdown_id" => "2",
                "parent_id" => "4",
                "value" => "Audi",
            ],
            [
                "ymm_value_id" => "6",
                "dropdown_id" => "3",
                "parent_id" => "5",
                "value" => "A4",
            ],
            [
                "ymm_value_id" => "7",
                "dropdown_id" => "1",
                "parent_id" => "0",
                "value" => "2122",
            ],
            [
                "ymm_value_id" => "8",
                "dropdown_id" => "2",
                "parent_id" => "7",
                "value" => "GMC",
            ],
            [
                "ymm_value_id" => "9",
                "dropdown_id" => "3",
                "parent_id" => "8",
                "value" => "Sierra 1500",
            ],
        ];
        $ymm_product_1 = $helper->getCurrentStoreProductIds(5, "|");
        $ymm_product_1_skus = null;
        $ymm_product_1_ids = null;
        $ymm_product_2 = $helper->getCurrentStoreProductIds(7, "|");
        $ymm_product_2_ids = null;
        if (isset($ymm_product_1["product_sku"])) {
            $ymm_product_1_skus = $ymm_product_1["product_sku"];
        }
        if (isset($ymm_product_2["product_sku"])) {
            $ymm_product_2_skus = $ymm_product_2["product_sku"];
        }
        if (isset($ymm_product_1["product_ids"])) {
            $ymm_product_1_ids = $ymm_product_1["product_ids"];
        }
        if (isset($ymm_product_2["product_ids"])) {
            $ymm_product_2_ids = $ymm_product_2["product_ids"];
        }
        $magebees_finder_map_value = [
            [
                "map_value_id" => "2",
                "ymm_value_id" => "6",
                "product_id" => $ymm_product_1_ids,
                "sku" => $ymm_product_1_skus,
            ],
            [
                "map_value_id" => "3",
                "ymm_value_id" => "9",
                "product_id" => $ymm_product_2_ids,
                "sku" => $ymm_product_1_skus,
            ],
        ];
        if (
            count($magebees_finder) > 0 &&
            count($magebees_finder_dropdowns) > 0 &&
            count($magebees_finder_ymm_value) > 0 &&
            count($magebees_finder_map_value) > 0
        ) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_finder")
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_finder_dropdowns")
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_finder_ymm_value")
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_finder_map_value")
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_finder"),
                $magebees_finder
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_finder_dropdowns"),
                $magebees_finder_dropdowns
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_finder_ymm_value"),
                $magebees_finder_ymm_value
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_finder_map_value"),
                $magebees_finder_map_value
            );
            $output->writeln(
                "<info>Magebees_Finder Sample Data Insterted Successfully.</info>"
            );
        }
        /* Finder End */
		/* Image gallery Start*/
//		$magebees_imagegallery = ImageGalleryData::getImagegallery($first_store_id);
//
//        if (count($magebees_imagegallery) > 0) {
//            $this->connection->query(
//                "DELETE FROM " .
//                    $this->resource->getTableName("magebees_imagegallery")
//            );
//            $this->connection->insertMultiple(
//                $this->resource->getTableName("magebees_imagegallery"),
//                $magebees_imagegallery
//            );
//            $output->writeln(
//                "<info>Magebees_ImageGallery Sample Data Insterted Successfully.</info>"
//            );
//        }
        /* Image gallery End */
		/* Improve Layer brands start */
		$magebees_layernav_brand = LayernavBrandData::getLayernavBrand();

        if (count($magebees_layernav_brand) > 0) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_layernav_brand")
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_layernav_brand"),
                $magebees_layernav_brand
            );
            $output->writeln(
                "<info>Magebees_Layerednavigation Brand Slider Sample Data Insterted Successfully.</info>"
            );
        }
        /* Improve Layer brands end */

		/* Pagebanner Start */

		$magebees_pagebanner = PagebannerData::getPagebanner($page_baanner_store_id);


        if (count($magebees_pagebanner) > 0) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_pagebanner")
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_pagebanner"),
                $magebees_pagebanner
            );
            $output->writeln(
                "<info>Magebees_Pagebanner Sample Data Insterted Successfully.</info>"
            );
        }
        /* Pagebanner End */ /* product label start */ $product_label_1 = $helper->getStoreProductIds(5);
        $product_label_1_skus = null;
        $product_label_2 = $helper->getStoreProductIds(7);
        $product_label_2_skus = null;
        if (isset($product_label_1["product_sku"])) {
            $product_label_1_skus = $product_label_1["product_sku"];
        }
        if (isset($product_label_2["product_sku"])) {
            $product_label_2_skus = $product_label_2["product_sku"];
        }
        $magebees_productlabel = [
            [
                "label_id" => "1",
                "title" => "New Label",
                "is_active" => "1",
                "hide" => "1",
                "sort_order" => "0",
                "stores" => $storeIds["store_ids_str"],
                "customer_group_ids" => $customerGroupsIds,
                "prod_text" => "NEW",
                "prod_image" => "/l/a/label-new.png",
                "prod_image_width" => "50",
                "prod_image_height" => "23",
                "state" => "",
                "prod_position" => "TL",
                "prod_text_color" => "#FFFFFF",
                "prod_text_size" => "13",
                "cat_text" => "NEW",
                "cat_image" => "/l/a/label-new.png",
                "cat_image_width" => "50",
                "cat_image_height" => "23",
                "cat_position" => "TL",
                "cat_text_color" => "#FFFFFF",
                "cat_text_size" => "13",
                "include_sku" => null,
                "include_cat" => null,
                "attr_code" => "",
                "attr_value" => "",
                "is_new" => "0",
                "is_sale" => "0",
                "date_enabled" => "0",
                "from_date" => null,
                "to_date" => null,
                "price_enabled" => "0",
                "from_price" => "0.0000",
                "to_price" => "0.0000",
                "by_price" => "0",
                "stock_status" => "0",
                "cond_serialize" =>
                    '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Product","attribute":"sku","operator":"()","value":"' .
                    $product_label_1_skus .
                    '","is_value_processed":false}]}',
            ],
            [
                "label_id" => "2",
                "title" => "Sale",
                "is_active" => "1",
                "hide" => "0",
                "sort_order" => "0",
                "stores" => $storeIds["store_ids_str"],
                "customer_group_ids" => $customerGroupsIds,
                "prod_text" => "-{SAVE_PERCENT}%",
                "prod_image" => "/l/a/label-sale.png",
                "prod_image_width" => "50",
                "prod_image_height" => "23",
                "state" => "",
                "prod_position" => "TR",
                "prod_text_color" => "#FFFFFF",
                "prod_text_size" => "13",
                "cat_text" => "-{SAVE_PERCENT}%",
                "cat_image" => "/l/a/label-sale.png",
                "cat_image_width" => "50",
                "cat_image_height" => "23",
                "cat_position" => "TR",
                "cat_text_color" => "#FFFFFF",
                "cat_text_size" => "13",
                "include_sku" => null,
                "include_cat" => null,
                "attr_code" => "",
                "attr_value" => "",
                "is_new" => "0",
                "is_sale" => "2",
                "date_enabled" => "0",
                "from_date" => null,
                "to_date" => null,
                "price_enabled" => "0",
                "from_price" => "0.0000",
                "to_price" => "0.0000",
                "by_price" => "0",
                "stock_status" => "0",
                "cond_serialize" =>
                    '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"0","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Product","attribute":"sku","operator":"==","value":"' .
                    $product_label_2_skus .
                    '","is_value_processed":false}]}',
            ],
            [
                "label_id" => "3",
                "title" => "Sold Out",
                "is_active" => "1",
                "hide" => "0",
                "sort_order" => "0",
                "stores" => $storeIds["store_ids_str"],
                "customer_group_ids" => $customerGroupsIds,
                "prod_text" => "SOLD OUT",
                "prod_image" => "/l/a/label-sold-out.png",
                "prod_image_width" => "82",
                "prod_image_height" => "23",
                "state" => "",
                "prod_position" => "TL",
                "prod_text_color" => "#FFFFFF",
                "prod_text_size" => "12",
                "cat_text" => "SOLD OUT",
                "cat_image" => "/l/a/label-sold-out.png",
                "cat_image_width" => "82",
                "cat_image_height" => "23",
                "cat_position" => "TL",
                "cat_text_color" => "#FFFFFF",
                "cat_text_size" => "12",
                "include_sku" => null,
                "include_cat" => null,
                "attr_code" => "",
                "attr_value" => "",
                "is_new" => "0",
                "is_sale" => "0",
                "date_enabled" => "0",
                "from_date" => null,
                "to_date" => null,
                "price_enabled" => "0",
                "from_price" => "0.0000",
                "to_price" => "0.0000",
                "by_price" => "0",
                "stock_status" => "2",
                "cond_serialize" =>
                    '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all"}',
            ],
        ];
        if (count($magebees_productlabel) > 0) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_productlabel")
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_productlabel"),
                $magebees_productlabel
            );
            $output->writeln(
                "<info>Magebees_Productlabel Sample Data Insterted Successfully.</info>"
            );
        }
        /* product label end */
		/* Product Listing Start */
//		$magebees_productlisting = [
//  ['listing_id' => '1','title' => 'Fashion Layout New Arrivals','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"new","collection_type":"manually","display_by":"all","date_enabled":"0","new_threshold":"10","template":"grid"}','slider_options' => '{"num_of_prod":"12","enable_slider":"1","items_per_slide":"4","autoplay":"1","delay_time":"8000","mouse_enter":"1","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"1","grab_cur":"1","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"Weekend Sellers","display_desc":"1","short_desc":"New arrivals","sort_by":"price","sort_order":"DESC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '2','title' => 'Fashion Layout Best Seller Great Selection','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"manually","display_by":"all","bundle_config":"parent","best_time":"30","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"15","enable_slider":"1","items_per_slide":"4","autoplay":"1","delay_time":"9000","mouse_enter":"1","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"1","grab_cur":"1","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"Great Selection","display_desc":"1","short_desc":"Best Seller Items","sort_by":"name","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '3','title' => 'Best Seller Sidebar','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"both","display_by":"all","bundle_config":"parent","best_time":"30","order_status":"complete","template":"sidebar"}','slider_options' => '{"num_of_prod":"3","enable_slider":"0","items_per_row":"2","items_per_page":"3","autoscroll":"0","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"Best Sellers","display_desc":"1","short_desc":"Best Sellers Short Description","sort_by":"random","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"1","custom_class":"block-ProductsListing"}'],
//  ['listing_id' => '4','title' => 'Furniture Most Popular Products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"mostview","collection_type":"manually","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"0","view_more":"1","view_more_btn_txt":"+ Show More","view_more_path":"most-popular-products"}','display_settings' => '{"display_heading":"1","heading":"Most popular","display_desc":"1","short_desc":"Top View In This Week","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '5','title' => 'Cosmetic layout BestSeller','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"both","display_by":"all","bundle_config":"child","best_time":"30","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"1","items_per_slide":"6","autoplay":"1","delay_time":"7000","mouse_enter":"1","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","view_more":"0"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"random","sort_order":"ASC","display_price":"0","display_product_short_description":"1","product_short_description_length":"10","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":"swiper_items5"}'],
//  ['listing_id' => '6','title' => 'Cosmetic layout New Arrivals','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"new","collection_type":"both","display_by":"all","date_enabled":"0","new_threshold":"30","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"1","items_per_slide":"6","autoplay":"1","delay_time":"7000","mouse_enter":"1","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","view_more":"0"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"random","sort_order":"ASC","display_price":"0","display_product_short_description":"1","product_short_description_length":"10","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":"swiper_items5"}'],
//  ['listing_id' => '7','title' => 'Cosmetic layout Top Rated','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"1","items_per_slide":"6","autoplay":"1","delay_time":"7000","mouse_enter":"0","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","view_more":"0"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"random","sort_order":"ASC","display_price":"0","display_product_short_description":"1","product_short_description_length":"10","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":"swiper_items5"}'],
//  ['listing_id' => '8','title' => 'Trending Products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"auto","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"0","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"TRENDING PRODUCTS","display_desc":"0","sort_by":"random","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '9','title' => 'Electronic Mega Sale Hurry up!','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"all","template":"sidebar"}','slider_options' => '{"num_of_prod":"3","enable_slider":"0","items_per_row":"3","items_per_page":"3","autoscroll":"0","view_more":"1","view_more_btn_txt":"View More","view_more_path":"mega-sale-hurry-up"}','display_settings' => '{"display_heading":"1","heading":"Mega Sale Hurry up!","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"1","custom_class":""}'],
//  ['listing_id' => '10','title' => 'Electronic Top Rated products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"auto","display_by":"all","template":"sidebar"}','slider_options' => '{"num_of_prod":"3","enable_slider":"0","items_per_row":"3","items_per_page":"3","autoscroll":"0","view_more":"1","view_more_btn_txt":"View More","view_more_path":"top-rated-products"}','display_settings' => '{"display_heading":"1","heading":"Top rated products","display_desc":"0","sort_by":"random","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '11','title' => 'Electronic New Products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"new","collection_type":"both","display_by":"all","date_enabled":"0","new_threshold":"30","template":"sidebar"}','slider_options' => '{"num_of_prod":"3","enable_slider":"0","items_per_row":"3","items_per_page":"3","autoscroll":"0","view_more":"1","view_more_btn_txt":"View More","view_more_path":"new-products"}','display_settings' => '{"display_heading":"1","heading":"New Products","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '12','title' => 'AutoParts Layout Top Product','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"new","collection_type":"both","display_by":"all","date_enabled":"0","new_threshold":"30","template":"grid"}','slider_options' => '{"num_of_prod":"10","enable_slider":"0","items_per_row":"5","items_per_page":"10","autoscroll":"0","view_more":"1","view_more_btn_txt":"All Products","view_more_path":"top-products"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"random","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '13','title' => 'AutoParts Layout Best Selling','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"both","display_by":"all","bundle_config":"child","best_time":"30","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"10","enable_slider":"0","items_per_row":"5","items_per_page":"10","autoscroll":"0","view_more":"1","view_more_btn_txt":"All Products","view_more_path":"bestselling"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"random","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '14','title' => 'AutoParts Layout Sale Item','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"auto","display_by":"all","date_enabled":"0","new_from_date":"","new_to_date":"","new_threshold":"30","bundle_config":"child","best_time":"30","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"10","enable_slider":"0","items_per_row":"5","items_per_page":"10","autoscroll":"0","view_more":"1","view_more_btn_txt":"All Products","view_more_path":"sale-item"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"random","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '15','title' => 'Medical Popular products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"auto","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"0","view_more":"1","view_more_btn_txt":"See all products","view_more_path":"popular-products"}','display_settings' => '{"display_heading":"1","heading":"POPULAR PRODUCTS","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"1","custom_class":""}'],
//  ['listing_id' => '16','title' => 'Kids shop our favourites','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"auto","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"0","view_more":"1","view_more_btn_txt":"Load More","view_more_path":"shop-our-favourites"}','display_settings' => '{"display_heading":"1","heading":"Shop our favourites","display_desc":"1","short_desc":"Trendy now","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '17','title' => 'Best toy sellers','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"auto","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"4","enable_slider":"0","items_per_row":"4","items_per_page":"4","autoscroll":"0","view_more":"1","view_more_btn_txt":"Load More","view_more_path":"best-toy-sellers"}','display_settings' => '{"display_heading":"1","heading":"Best toy sellers","display_desc":"1","short_desc":"Popular product","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '18','title' => 'Fashion 2 Layout New Arrivals','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"new","collection_type":"both","display_by":"all","date_enabled":"0","new_threshold":"10","template":"grid"}','slider_options' => '{"num_of_prod":"12","enable_slider":"1","items_per_slide":"4","autoplay":"1","delay_time":"8000","mouse_enter":"1","auto_height":"0","nav_arr":"1","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"Weekend Sellers","display_desc":"1","short_desc":"New arrivals","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '19','title' => 'Fashion 2 Layout Best Seller Great Selection','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"both","display_by":"all","bundle_config":"child","best_time":"30","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"15","enable_slider":"1","items_per_slide":"4","autoplay":"1","delay_time":"9000","mouse_enter":"1","auto_height":"0","nav_arr":"1","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"Great Selection","display_desc":"1","short_desc":"Best sellers","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '20','title' => 'Decor Layout New Arrivals','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"new","collection_type":"both","display_by":"all","date_enabled":"0","new_threshold":"30","template":"grid"}','slider_options' => '{"num_of_prod":"10","enable_slider":"0","items_per_row":"5","items_per_page":"10","autoscroll":"0","view_more":"1","view_more_btn_txt":"Shop all products","view_more_path":"shop-all-products"}','display_settings' => '{"display_heading":"1","heading":"Featured Products","display_desc":"1","short_desc":"New Arrivals","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '21','title' => 'Decor Layout Trending Items','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"both","display_by":"all","bundle_config":"child","best_time":"30","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"5","enable_slider":"1","items_per_slide":"5","autoplay":"0","mouse_enter":"0","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"Trending Items","display_desc":"1","short_desc":"Favorite Products","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '22','title' => 'Footwear Featured Products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"30","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"1","loading_type":"1","text_no_more":"No More Products","threshold":"30","show_page_no":"0","label_prev_button":"Load Less","label_next_button":"Load More","load_button_style":""}','display_settings' => '{"display_heading":"1","heading":"Featured Products","display_desc":"1","short_desc":"Follow the most <a href=\\"newest-snakers.html\\">popular trends<\\/a>","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '23','title' => 'Footwear Treanding Collection','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"30","enable_slider":"1","items_per_slide":"4","autoplay":"1","delay_time":"6000","mouse_enter":"0","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"0","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"Trending collection","display_desc":"1","short_desc":"Most popular <a href=\\"air-zoom-pegasus.html\\">shoes trends<\\/a>","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '24','title' => 'Jewellery Featured product','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"cat","template":"grid"}','slider_options' => '{"num_of_prod":"4","enable_slider":"0","items_per_row":"4","items_per_page":"4","autoscroll":"0","view_more":"1","view_more_btn_txt":"See All Products","view_more_path":"jewellery-featured-product"}','display_settings' => '{"display_heading":"1","heading":"Featured products","display_desc":"1","short_desc":"You wear your jewellery, don\'t let it wear you","sort_by":"random","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":"jw_fp-sec"}'],
//  ['listing_id' => '25','title' => 'Vitamin Best Seller ','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"both","display_by":"all","bundle_config":"parent","best_time":"30","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"30","enable_slider":"0","items_per_row":"5","items_per_page":"15","autoscroll":"0","view_more":"1","view_more_btn_txt":"Best Seller","view_more_path":"best-seller-products"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '26','title' => 'Guns New Products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"0","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"New Products","display_desc":"1","short_desc":"Best special price for you","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"1","custom_class":""}'],
//  ['listing_id' => '27','title' => 'Gaming Best Seller','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"both","display_by":"all","bundle_config":"child","best_time":"30","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"0","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"Best seller of the week","display_desc":"1","short_desc":"Our featured products","sort_by":"random","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '28','title' => 'Bakery New Item','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"new","collection_type":"both","display_by":"all","date_enabled":"0","new_threshold":"10","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"0","view_more":"1","view_more_btn_txt":"Shop Now","view_more_path":"donut"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"1","product_short_description_length":"10","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"1","custom_class":""}'],
//  ['listing_id' => '29','title' => 'Bakery Bestseller','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"both","display_by":"all","bundle_config":"child","best_time":"30","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"0","view_more":"1","view_more_btn_txt":"Shop Now","view_more_path":"donut"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"1","product_short_description_length":"10","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"1","custom_class":""}'],
//  ['listing_id' => '30','title' => 'Bakery Best Offers','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"mostview","collection_type":"both","display_by":"all","template":"sidebar"}','slider_options' => '{"num_of_prod":"6","enable_slider":"0","items_per_row":"3","items_per_page":"6","autoscroll":"0","view_more":"1","view_more_btn_txt":"Don\'t miss","view_more_path":"donut"}','display_settings' => '{"display_heading":"1","heading":"BEST OFFERS","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"1","product_short_description_length":"10","display_addtocart":"1","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"1","custom_class":"bestofr_list"}'],
//  ['listing_id' => '31','title' => 'Trending Products - Wine','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"both","display_by":"all","bundle_config":"both","best_time":"10","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"8","enable_slider":"0","items_per_row":"4","items_per_page":"8","autoscroll":"0","view_more":"1","view_more_btn_txt":"Collections","view_more_path":"red-wines"}','display_settings' => '{"display_heading":"1","heading":"TRENDING PRODUCTS","display_desc":"1","short_desc":"Our best spirits","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"1","custom_class":""}'],
//  ['listing_id' => '32','title' => 'Mega Store Special Store Collection','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"16","enable_slider":"1","items_per_slide":"6","autoplay":"1","delay_time":"8000","mouse_enter":"0","auto_height":"0","nav_arr":"1","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","grid":"1","view_more":"1","view_more_btn_txt":"See More <svg xmlns=\\"http:\\/\\/www.w3.org\\/2000\\/svg\\" width=\\"24\\" height=\\"24\\" viewBox=\\"0 0 24 24\\" fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\" class=\\"st-icon\\"><polyline points=\\"9 18 15 12 9 6\\"><\\/polyline><\\/svg>","view_more_path":"special-store-collection"}','display_settings' => '{"display_heading":"1","heading":"Special store collection","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '33','title' => 'Mega Store - Inspired by your browsing history','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"auto","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"12","enable_slider":"1","items_per_slide":"6","autoplay":"1","delay_time":"8000","mouse_enter":"0","auto_height":"0","nav_arr":"1","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","grid":"0","view_more":"1","view_more_btn_txt":"See More <svg xmlns=\\"http:\\/\\/www.w3.org\\/2000\\/svg\\" width=\\"24\\" height=\\"24\\" viewBox=\\"0 0 24 24\\" fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\" class=\\"st-icon\\"><polyline points=\\"9 18 15 12 9 6\\"><\\/polyline><\\/svg>","view_more_path":"special-store-collection"}','display_settings' => '{"display_heading":"1","heading":"Inspired by your browsing history","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '34','title' => 'Mega Store - Related to items you\'ve viewed','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"auto","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"12","enable_slider":"1","items_per_slide":"6","autoplay":"1","delay_time":"8000","mouse_enter":"0","auto_height":"0","nav_arr":"1","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","grid":"0","view_more":"1","view_more_btn_txt":"See More <svg xmlns=\\"http:\\/\\/www.w3.org\\/2000\\/svg\\" width=\\"24\\" height=\\"24\\" viewBox=\\"0 0 24 24\\" fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\" class=\\"st-icon\\"><polyline points=\\"9 18 15 12 9 6\\"><\\/polyline><\\/svg>","view_more_path":"special-store-collection"}','display_settings' => '{"display_heading":"1","heading":"Related to items you\'ve viewed","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '35','title' => 'Mega Store - Continue Shopping For','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"all","template":"sidebar"}','slider_options' => '{"num_of_prod":"18","enable_slider":"1","items_per_slide":"2","autoplay":"1","delay_time":"8000","mouse_enter":"0","auto_height":"0","nav_arr":"1","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","grid":"1","view_more":"1","view_more_btn_txt":"See More <svg xmlns=\\"http:\\/\\/www.w3.org\\/2000\\/svg\\" width=\\"24\\" height=\\"24\\" viewBox=\\"0 0 24 24\\" fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\" class=\\"st-icon\\"><polyline points=\\"9 18 15 12 9 6\\"><\\/polyline><\\/svg>","view_more_path":"continue-shopping-for"}','display_settings' => '{"display_heading":"1","heading":"Continue shopping for","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '36','title' => 'Mega Store Top rated products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"all","template":"sidebar"}','slider_options' => '{"num_of_prod":"18","enable_slider":"1","items_per_slide":"2","autoplay":"1","delay_time":"7000","mouse_enter":"0","auto_height":"0","nav_arr":"1","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","grid":"1","view_more":"1","view_more_btn_txt":"See More <svg xmlns=\\"http:\\/\\/www.w3.org\\/2000\\/svg\\" width=\\"24\\" height=\\"24\\" viewBox=\\"0 0 24 24\\" fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\" class=\\"st-icon\\"><polyline points=\\"9 18 15 12 9 6\\"><\\/polyline><\\/svg>","view_more_path":"top-rated-products"}','display_settings' => '{"display_heading":"1","heading":"Top rated products","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//  ['listing_id' => '37','title' => 'Mega Store - Automative essentials','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"both","display_by":"all","template":"sidebar"}','slider_options' => '{"num_of_prod":"18","enable_slider":"1","items_per_slide":"2","autoplay":"1","delay_time":"9000","mouse_enter":"0","auto_height":"0","nav_arr":"1","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","grid":"1","view_more":"1","view_more_btn_txt":"See More <svg xmlns=\\"http:\\/\\/www.w3.org\\/2000\\/svg\\" width=\\"24\\" height=\\"24\\" viewBox=\\"0 0 24 24\\" fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\" class=\\"st-icon\\"><polyline points=\\"9 18 15 12 9 6\\"><\\/polyline><\\/svg>","view_more_path":"automative-essentials"}','display_settings' => '{"display_heading":"1","heading":"Automative Essentials","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"0","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":""}'],
//    ['listing_id' => '39','title' => 'Pet New Arrivals','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"new","collection_type":"both","display_by":"all","date_enabled":"0","new_threshold":"60","template":"grid"}','slider_options' => '{"num_of_prod":"10","enable_slider":"0","items_per_row":"5","items_per_page":"10","autoscroll":"0","view_more":"0"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"0","custom_class":""}'],
//    ['listing_id' => '40','title' => 'Pet Best Products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"manually","display_by":"all","bundle_config":"child","best_time":"60","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"10","enable_slider":"0","items_per_row":"5","items_per_page":"10","autoscroll":"0","view_more":"0"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"0","custom_class":""}'],
//    ['listing_id' => '41','title' => 'Pet Offer Products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"manually","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"10","enable_slider":"0","items_per_row":"5","items_per_page":"10","autoscroll":"0","view_more":"0"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"0","display_addtowishlist":"1","display_outofstock":"0","custom_class":""}'],
//    ['listing_id' => '42','title' => 'Pet Top Rating','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"bestseller","collection_type":"manually","display_by":"all","bundle_config":"child","best_time":"60","order_status":"complete","template":"grid"}','slider_options' => '{"num_of_prod":"10","enable_slider":"0","items_per_row":"5","items_per_page":"10","autoscroll":"0","view_more":"0"}','display_settings' => '{"display_heading":"0","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"0","custom_class":""}'],
//    ['listing_id' => '43','title' => 'Pet Featured Products','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"manually","display_by":"all","template":"grid"}','slider_options' => '{"num_of_prod":"5","enable_slider":"1","items_per_slide":"5","autoplay":"1","delay_time":"8000","mouse_enter":"0","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","grid":"0","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"Featured products","display_desc":"1","short_desc":"Today\'s featured deals","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"0","custom_class":""}'],
//	['listing_id' => '44','title' => 'Bike New Collections','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"new","collection_type":"both","display_by":"all","date_enabled":"0","new_threshold":"60","template":"grid"}','slider_options' => '{"num_of_prod":"10","enable_slider":"1","items_per_slide":"5","autoplay":"1","delay_time":"7000","mouse_enter":"0","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"1","grid":"0","view_more":"1","view_more_btn_txt":"More Item","view_more_path":"more"}','display_settings' => '{"display_heading":"1","heading":"New Collections","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"1","display_product_short_description":"0","display_addtocart":"1","display_addtocompare":"1","display_addtowishlist":"1","display_outofstock":"0","custom_class":""}'],
//    ['listing_id' => '45','title' => 'Bike New Model','stores' => $storeIds["store_ids_str"],'category_ids' => '','status' => '1','general' => '{"product_type_options":"featured","collection_type":"auto","display_by":"all","template":"list_style1"}','slider_options' => '{"num_of_prod":"3","enable_slider":"1","items_per_slide":"1","autoplay":"1","delay_time":"7000","mouse_enter":"0","auto_height":"0","nav_arr":"1","pagination":"0","loop":"1","scrollbar":"0","grab_cur":"1","grid":"0","view_more":"0"}','display_settings' => '{"display_heading":"1","heading":"new model","display_desc":"0","sort_by":"position","sort_order":"ASC","display_price":"0","display_product_short_description":"1","product_short_description_length":"100","display_addtocart":"0","display_addtocompare":"0","display_addtowishlist":"0","display_outofstock":"0","custom_class":"swiper_items1"}']
//
//];
//
//        $magebees_productlisting_select_products = [];
//        $listing_ids = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,39,40,41,42,43,44,45];
//        $list_count = 1;
//		$listing_products_ids_arr = array();
//        foreach ($listing_ids as $listing_id):
//            $listing_products = $helper->getStoreProductIds(25);
//            if (isset($listing_products["product_ids"])) {
//                $listing_products_ids = $listing_products["product_ids"];
//                $listing_products_ids_arr = explode(",", $listing_products_ids);
//            }
//            if (isset($listing_products["product_sku"])) {
//                $listing_products_skus = $listing_products["product_sku"];
//                $listing_products_skus_arr = explode(
//                    ",",
//                    $listing_products_skus
//                );
//            }
//            $productCount = count($listing_products_ids_arr);
//            for ($i = 0; $i <= $productCount; $i++) {
//                if (
//                    isset($listing_products_skus_arr[$i]) &&
//                    isset($listing_products_ids_arr[$i])
//                ) {
//                    $magebees_productlisting_select_products[] = [
//                        "select_product_id" => $list_count,
//                        "listing_id" => $listing_id,
//                        "sku" => $listing_products_skus_arr[$i],
//                        "product_id" => $listing_products_ids_arr[$i],
//                    ];
//                    $list_count++;
//                }
//            }
//        endforeach;
//        if (count($magebees_productlisting) > 0 ) {
//		$this->connection->query("DELETE FROM " .$this->resource->getTableName("magebees_productlisting")
//		);
//		$this->connection->insertMultiple($this->resource->getTableName("magebees_productlisting"),                $magebees_productlisting );
//		$output->writeln( "<info>Magebees_Productlisting Sample Data Insterted Successfully.</info>");
//		}
//		if (count($magebees_productlisting_select_products) > 0) {
//			$this->connection->query( "DELETE FROM " .$this->resource->getTableName(                        "magebees_productlisting_select_products"                    )            );
//			$this->connection->insertMultiple($this->resource->getTableName("magebees_productlisting_select_products"),$magebees_productlisting_select_products );
//			$output->writeln("<info>Magebees_Productlisting Products Sample Data Insterted Successfully.</info>"            );
//		}

        /* Product Listing end */
		/* Notification Start */
		$magebees_promotionsnotification = PromotionsnotificationData::getPromotionsnotification($current_date_time,$to_date);

        $magebees_notification_customer = [];
        $notification_ids = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26];
        $notification_customer_id = 0;
        foreach ($customerGroupsIds_arr as $customer_groupid){
            foreach ($notification_ids as $notification_id){
                $notification_customer_id++;
                $magebees_notification_customer[] = [
                    "notification_customer_id" => $notification_customer_id,
                    "notification_id" => $notification_id,
                    "customer_ids" => $customer_groupid,
                ];
            }
        }
        $magebees_notification_store = [];
        $notification_ids = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26];
        $store_ids = 0;
        $store_ids_arr = [0];
        foreach ($store_ids_arr as $storeId){
            foreach ($notification_ids as $notification_id){
                $store_ids++;
                $magebees_notification_store[] = [
                    "notification_store_id" => $store_ids,
                    "notification_id" => $notification_id,
                    "store_ids" => $storeId,
                ];
            }
        }
        $magebees_notification_page = [
            [
                "notification_page_id" => "69",
                "notification_id" => "1",
                "pages" => $electronic_style,
            ],
            [
                "notification_page_id" => "103",
                "notification_id" => "4",
                "pages" => $cosmetic_style,
            ],
            [
                "notification_page_id" => "108",
                "notification_id" => "5",
                "pages" => $electronic_style,
            ],
            [
                "notification_page_id" => "109",
                "notification_id" => "6",
                "pages" => $autoparts_style,
            ],
            [
                "notification_page_id" => "111",
                "notification_id" => "8",
                "pages" => $medical_style,
            ],
            [
                "notification_page_id" => "115",
                "notification_id" => "7",
                "pages" => $furniture_style,
            ],
            [
                "notification_page_id" => "116",
                "notification_id" => "2",
                "pages" => $fashion_style,
            ],
            [
                "notification_page_id" => "117",
                "notification_id" => "3",
                "pages" => $fashion_style,
            ],
            [
                "notification_page_id" => "118",
                "notification_id" => "9",
                "pages" => $fashion2_style,
            ],
            [
                "notification_page_id" => "119",
                "notification_id" => "10",
                "pages" => $fashion2_style,
            ],
            [
                "notification_page_id" => "120",
                "notification_id" => "11",
                "pages" => $kids_style,
            ],
			[
                "notification_page_id" => "121",
                "notification_id" => "12",
                "pages" => $decor_style,
            ],
			[
                "notification_page_id" => "122",
                "notification_id" => "13",
                "pages" => $decor_style,
            ],
			[
                "notification_page_id" => "123",
                "notification_id" => "14",
                "pages" => $footwear_style,
            ],
			[
                "notification_page_id" => "124",
                "notification_id" => "15",
                "pages" => $jewellery_style,
            ],
			[
                "notification_page_id" => "125",
                "notification_id" => "16",
                "pages" => $jewellery_style,
            ],
			[
                "notification_page_id" => "126",
                "notification_id" => "17",
                "pages" => $vitamin_style,
            ],
			[
                "notification_page_id" => "127",
                "notification_id" => "18",
                "pages" => $gun_style,
            ],
			[
                "notification_page_id" => "128",
                "notification_id" => "19",
                "pages" => $gaming_style,
            ],
			[
                "notification_page_id" => "129",
                "notification_id" => "20",
                "pages" => $bakery_style,
            ],
			[
                "notification_page_id" => "130",
                "notification_id" => "21",
                "pages" => $wine_style,
            ],
			[
                "notification_page_id" => "131",
                "notification_id" => "22",
                "pages" => $megastore_style,
            ],
			[
                "notification_page_id" => "132",
                "notification_id" => "23",
                "pages" => $pet_style,
            ],

			[
                "notification_page_id" => "134",
                "notification_id" => "25",
                "pages" => $bike_style,
            ]

        ];

		if (
            count($magebees_promotionsnotification) > 0 &&
            count($magebees_notification_customer) > 0 &&
            count($magebees_notification_store) > 0 &&
            count($magebees_notification_page) > 0
        ) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName(
                        "magebees_promotionsnotification"
                    )
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName(
                        "magebees_notification_customer"
                    )
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_notification_store")
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_notification_page")
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName(
                    "magebees_promotionsnotification"
                ),
                $magebees_promotionsnotification
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_notification_customer"),
                $magebees_notification_customer
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_notification_store"),
                $magebees_notification_store
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_notification_page"),
                $magebees_notification_page
            );
            $output->writeln(
                "<info>Magebees_Promotionsnotification Sample Data Insterted Successfully.</info>"
            );
        }
        /* Notification End */


		/* Navigation Menu Start */
		$magebees_menucreatorgroup = NavigationMenuData::getMenucreatorgroup($current_date_time);
		$magebees_menucreator = NavigationMenuData::getMenucreator($storeIds,$currentdate,$baseUrl,$about_us);

		if (
            count($magebees_menucreatorgroup) > 0 &&
            count($magebees_menucreator) > 0
        ) {
          $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_menucreatorgroup")
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_menucreatorgroup"),
                $magebees_menucreatorgroup
            );
           $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_menucreator")
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_menucreator"),
                $magebees_menucreator
            );
            $output->writeln(
                "<info>Magebees_Navigationmenu Sample Data Insterted Successfully.</info>"
            );
        }
        /* Navigation Menu End */

		/* responsive banner slider start */
		$responsivebannerslider_group = ResponsivebannersliderData::getResponsivebannersliderGroup();
		$responsivebannerslider_slide = ResponsivebannersliderData::getResponsivebannersliderSlide();

        $responsivebannerslider_store = [];
        $slidergroup_ids = [1, 2, 3, 4, 5, 6, 7, 8,9,10,12,13,14,15,16,17,18,19];
        $store_ids = 0;
        $store_ids_arr = $storeIds["store_ids_arr"];
        foreach ($store_ids_arr as $storeId){
            foreach ($slidergroup_ids as $slidergroup_id){
                $store_ids++;
                $responsivebannerslider_store[] = [
                    "store_ids" => $store_ids,
                    "slidergroup_id" => $slidergroup_id,
                    "store_id" => $storeId,
                ];
			}
        }
        if (
            count($responsivebannerslider_group) > 0 &&
            count($responsivebannerslider_slide) > 0 &&
            count($responsivebannerslider_store) > 0
        ) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName(
                        "responsivebannerslider_group"
                    )
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName(
                        "responsivebannerslider_slide"
                    )
            );
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName(
                        "responsivebannerslider_store"
                    )
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("responsivebannerslider_group"),
                $responsivebannerslider_group
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("responsivebannerslider_slide"),
                $responsivebannerslider_slide
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("responsivebannerslider_store"),
                $responsivebannerslider_store
            );
            $output->writeln(
                "<info>Magebees_Responsivebannerslider Sample Data Insterted Successfully.</info>"
            );
        }
        /* responsive banner slider end */

		/* today deal */
		$today_deal_1 = $helper->getStoreProductIds(15);
        $today_deal_1_skus = null;

        $today_deal_2 = $helper->getStoreProductIds(20);
        $today_deal_2_skus = null;

		$today_deal_3 = $helper->getStoreProductIds(20);
        $today_deal_3_skus = null;

		$today_deal_4 = $helper->getStoreProductIds(10);
        $today_deal_4_skus = null;

		$today_deal_5 = $helper->getStoreProductIds(20);
        $today_deal_5_skus = null;

        if (isset($today_deal_1["product_sku"])) {
            $today_deal_1_skus = $today_deal_1["product_sku"];
        }
        if (isset($today_deal_2["product_sku"])) {
            $today_deal_2_skus = $today_deal_2["product_sku"];
        }
		if (isset($today_deal_3["product_sku"])) {
            $today_deal_3_skus = $today_deal_3["product_sku"];
        }
		if (isset($today_deal_4["product_sku"])) {
            $today_deal_4_skus = $today_deal_4["product_sku"];
        }
		if (isset($today_deal_5["product_sku"])) {
            $today_deal_5_skus = $today_deal_5["product_sku"];
        }
		$magebees_today_deal = [
			['today_deal_id' => '1','title' => 'New Deal','description' => 'New Deal Description','is_active' => '0','sort_order' => '0','stores' => $storeIds["store_ids_str"],'customer_group_ids' => $customerGroupsIds,'from_date' => $current_date_time,'to_date' => $to_date,'timer_format' => '0','layoutoptions' => '{"price":"0","cart":"0","compare":"0","wishlist":"0","out_of_stock":"0","total_products":"","pager":"0","enable_slider":"1","items_per_slide":"2","autoplay":"0","mouse_enter":"0","auto_height":"0","nav_arr":"0","pagination":"0","loop":"0","scrollbar":"0","grab_cur":"0"}','cond_serialize' => '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Product","attribute":"sku","operator":"()","value":"'.$today_deal_1_skus.'","is_value_processed":false}]}','unique_code' => NULL],
			['today_deal_id' => '2','title' => 'This Week\'s Deal','description' => NULL,'is_active' => '1','sort_order' => '0','stores' => $storeIds["store_ids_str"],'customer_group_ids' => $customerGroupsIds,'from_date' => $current_date_time,'to_date' => $to_date,'timer_format' => '0','layoutoptions' => '{"price":"0","cart":"0","compare":"0","wishlist":"0","out_of_stock":"0","total_products":"0","pager":"1","products_per_page":"8"}','cond_serialize' => '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Product","attribute":"sku","operator":"()","value":"'.$today_deal_2_skus.'","is_value_processed":false}]}','unique_code' => NULL],
			['today_deal_id' => '3','title' => 'Decor Deal','description' => NULL,'is_active' => '1','sort_order' => '0','stores' => $storeIds["store_ids_str"],'customer_group_ids' => $customerGroupsIds,'from_date' => $current_date_time,'to_date' => $to_date,'timer_format' => '0','layoutoptions' => '{"price":"0","cart":"0","compare":"0","wishlist":"0","out_of_stock":"0","total_products":"0","pager":"0","enable_slider":"1","items_per_slide":"2","autoplay":"0","mouse_enter":"0","auto_height":"0","nav_arr":"1","pagination":"0","loop":"1","scrollbar":"0","grab_cur":"1"}','cond_serialize' => '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Product","attribute":"sku","operator":"()","value":"'.$today_deal_3_skus.'","is_value_processed":false}]}','unique_code' => NULL],
			['today_deal_id' => '4','title' => 'Fresh goods from naturally products.','description' => 'Ingredients products 30% off','is_active' => '1','sort_order' => '0','stores' => $storeIds["store_ids_str"],'customer_group_ids' => $customerGroupsIds,'from_date' => $current_date_time,'to_date' => $to_date,'timer_format' => '0','layoutoptions' => '{"price":"0","cart":"0","compare":"0","wishlist":"0","out_of_stock":"0","total_products":"1","pager":"0","enable_slider":"0"}','cond_serialize' => '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Product","attribute":"sku","operator":"()","value":"'.$today_deal_4_skus.'","is_value_processed":false}]}','unique_code' => NULL],
			['today_deal_id' => '5','title' => 'Today\'s special deals!','description' => NULL,'is_active' => '1','sort_order' => '1','stores' => $storeIds["store_ids_str"],'customer_group_ids' => $customerGroupsIds,'from_date' => $current_date_time,'to_date' => $to_date,'timer_format' => '0','layoutoptions' => '{"price":"0","cart":"0","compare":"0","wishlist":"0","out_of_stock":"0","total_products":"","pager":"0","enable_slider":"0"}','cond_serialize' => '{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\\\CatalogRule\\\\Model\\\\Rule\\\\Condition\\\\Product","attribute":"sku","operator":"()","value":"'.$today_deal_5_skus.'","is_value_processed":false}]}','unique_code' => NULL]
		];


        if (count($magebees_today_deal) > 0) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName("magebees_today_deal")
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_today_deal"),
                $magebees_today_deal
            );
            $output->writeln(
                "<info>Magebees_TodayDealProducts Sample Data Insterted Successfully.</info>"
            );
        }
		/* today deal End*/


		/*  testimonial start */
		$magebees_customer_testimonials = [
			['testimonial_id' => '1','name' => 'Michella','email' => 'steev.smeeth@gmail.com','image' => '/p/h/photo3.png','website' => 'https://www.industry.com','company' => 'Marketing','address' => ' Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).','testimonial' => '“I love that makeup can make you feel confident and beautiful and i want to share that feeling with you.”','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '2','name' => 'Lorima','email' => 'lorima@gmail.com','image' => '/p/h/photo1.png','website' => 'https://www.industry.com','company' => 'manager','address' => 'The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.','testimonial' => '“I believe in manicures, I believe in
			overdressing, I believe in primping at
			leisure and wearing lipstick.”','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '3','name' => 'Rosemary','email' => 'steeve.smith@gmail.com','image' => '/p/h/photo2.png','website' => 'https://www.rackham.com','company' => 'makeup artist','address' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."','testimonial' => '“I love that makeup can make you feel confident and beautiful and i want to share that feeling with you.”','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '4','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '4','name' => 'DR. Lois Capps','email' => 'LoisCapps@test.com','image' => '/p/h/photo4.jpg','website' => 'https://www.google.com','company' => 'Opel','address' => '2200  Bayfield St
			Midland','testimonial' => 'The most exquisite pleasure in the practice of medicine comes from nudging a layman in the direction of terror, then bringing him back to safety again. The life so short, the craft so long to learn.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '5','name' => 'DR. Deny gloriya','email' => 'test@gmail.com','image' => '/p/h/photo5.jpg','website' => 'https://www.google.com','company' => 'BPL','address' => '2200  Bayfield St
			Midland','testimonial' => 'The doctor sees all the weakness of mankind; the lawyer all the wickedness, the theologian all the stupidity. Medicine is a science of uncertainty and the art of probability.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '4','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '6','name' => 'Samuel Hahnemann','email' => 'test@gmail.com','image' => '/p/h/photo6.jpg','website' => 'https://www.google.com','company' => 'Apolo','address' => '2200  Bayfield St
			Midland','testimonial' => 'If you aren’t willing to keep looking for light in the darkest of places
			without stopping even when it seems impossible, you will never succeed.
			Be like the stem cell, differentiate yourself from others','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '7','name' => 'DR. Jeorje Bailye','email' => 'test@gmail.com','image' => '/p/h/photo7.jpg','website' => 'https://www.google.com','company' => 'KIMS','address' => '2200  Bayfield St
			Midland','testimonial' => 'The most exquisite pleasure in the practice of medicine comes from nudging a layman in the direction of terror, then bringing him back to safety again. The life so short, the craft so long to learn.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '8','name' => 'Rosemary','email' => 'rosemary@gmail.com','image' => '/k/i/kids_tml1.jpg','website' => 'https://www.google.com','company' => 'co founder','address' => '','testimonial' => 'The finest clothing store for your babies is available here. Make your baby more unique with our best fabric and latest clothes designs.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '9','name' => 'Michella','email' => 'michella@gmail.com','image' => '/k/i/kids_tml2.jpg','website' => 'https://www.google.com','company' => 'Manager','address' => '','testimonial' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '4','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '10','name' => 'Lindgren','email' => 'lindgren@gmail.com','image' => '/k/i/kids_tml3.jpg','website' => 'https://www.google.com','company' => 'marketing','address' => '','testimonial' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. Lorem Ipsum is that it has a more-or-less','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '4','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '11','name' => 'Portner','email' => 'portner@gmail.com','image' => '/k/i/kids_tml5.jpg','website' => 'https://www.google.com','company' => 'co founder','address' => '','testimonial' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '12','name' => 'Daniel jacobs','email' => 'daniel.jacobs@gmail.com','image' => '','website' => '','company' => 'Yahoo, CEO','address' => '','testimonial' => 'Make your home as comfortable and attractive as possible and then get on with living. There’s more to life than decorating.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '13','name' => 'Jecob maricon','email' => 'jecob.maricon@yahoo.com','image' => '','website' => '','company' => 'Designer','address' => '','testimonial' => 'A designer knows when he has reached perfection not when there is nothing left to add but when there is nothing left to take away.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '4','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '14','name' => 'Mecron roy','email' => 'mecron.roy@gmail.com','image' => '','website' => '','company' => 'Manager','address' => '','testimonial' => 'Design is a funny word. Some people think design means how it looks. But of course, if you dig deeper, it’s really how it works.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '3','enabled_home' => '0','enabled_widget' => '0','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '15','name' => 'Author','email' => 'author@yahoo.com','image' => '','website' => '','company' => 'Designation','address' => '','testimonial' => 'Add customer reviews and testimonials to showcase your store’s happy customers with review star rating symbol.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '16','name' => 'Loomy androi','email' => 'loomy.androi@gmail.com','image' => '/j/e/jewellery_tml1.jpg','website' => '#','company' => 'Jewellery store','address' => 'Client Address','testimonial' => 'Why stop at one ring? The idea is to
			stack them up. Consider a multu coloured semi precious from ardon','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '17','name' => 'Laman harrish','email' => 'laman.harrish@yahoo.com','image' => '/j/e/jewellery_tml2.jpg','website' => '#','company' => 'Jewellery store','address' => 'Client Address','testimonial' => 'if there was a choice on spending a lot of monet an occessories of dresses.
			i always chose accessories.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '18','name' => 'Laren Davis','email' => 'lorem.ipsum@gmail.com','image' => '/j/e/jewellery_tml3.jpg','website' => '#','company' => 'Content writer','address' => 'Client Address','testimonial' => 'Add customer reviews and testimonials to showcase your store\'s happy customers.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '19','name' => 'Laman harrish','email' => 'laman.harrish@gmail.com','image' => '/j/e/jewellery_tml4.jpg','website' => '#','company' => 'Jewellery store','address' => 'Client Address','testimonial' => 'if there was a choice on spending a lot of monet an occessories of dresses.
			i always chose accessories.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '20','name' => 'Herry wilson','email' => 'herry.wilson@gmail.com','image' => '/j/e/jewellery_tml5.jpg','website' => '#','company' => 'Desinger','address' => 'Client Address','testimonial' => 'Customers can’t always tell you what they want but they can always tell you what’s wrong. Happy customers','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '21','name' => 'Samuel Hahnemann','email' => 'sam@info.net','image' => '/t/s/ts_photo1.jpg','website' => '','company' => 'CFL','address' => '','testimonial' => 'If someone has a gun and is trying to kill you, it would be reasonable to shoot back with your own gun. Waiting periods are only a step.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '22','name' => 'George Washington','email' => 'george@info.net','image' => '/t/s/ts_photo2.jpg','website' => '','company' => 'BFL','address' => '','testimonial' => 'As for gun control advocates, I have no hope whatever that any facts whatever will make the slightest dent in their thinking.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '23','name' => 'Clint Eastwood','email' => 'clint@info.com','image' => '/t/s/ts_photo3.jpg','website' => '','company' => '','address' => '','testimonial' => 'The most effective means of fighting crime in the United States is to outlaw the possession of any type of firearm by the civilian populace.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '24','name' => 'Laren Davis','email' => 'steev.smeeth@gmail.com','image' => '/t/s/ts_photo4.jpg','website' => '','company' => '','address' => '','testimonial' => 'The most effective means of fighting crime in the United States is to outlaw the possession of any type of firearm by the civilian populace.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '25','name' => 'Nick Jonas','email' => 'niki@gmail.com','image' => '','website' => '','company' => 'Nikki In.','address' => '','testimonial' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '26','name' => 'Jones lohrman','email' => 'jl@gmail.com','image' => '','website' => '','company' => '','address' => '','testimonial' => 'The smell of good bread baking is like the sound of lightly flowing water, is indescribable in its evocation of innocence and delight.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '27','name' => 'Will Smith','email' => 'will@gmail.com','image' => '','website' => '','company' => '','address' => '','testimonial' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '28','name' => 'Karl Markson','email' => 'krl@info.com','image' => '','website' => '','company' => '','address' => '','testimonial' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '29','name' => 'Rosemary dois','email' => 'steev.smeeth@gmail.com','image' => '/w/i/wine_tst-photo1.jpg','website' => '','company' => '','address' => '','testimonial' => 'Here\'s to all those who recognize that the most beautiful moments in life aren\'t just with wine but are about who we drink it with.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '30','name' => 'Charlotte Zoe','email' => 'steev.smeeth@gmail.com','image' => '/w/i/wine_tst-photo2.jpg','website' => '','company' => '','address' => '','testimonial' => 'Wine is one of the most civilized things in world and one of the most natural things of world that has been brought to the greatest perfection.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '31','name' => 'Jim Carter','email' => 'steev.smeeth@gmail.com','image' => '/w/i/wine_tst-photo4.jpg','website' => '','company' => '','address' => '','testimonial' => 'Quis facilisi nibh lacus aenean consequat tristique montes phasellus mi finibus praesent hac luctus dictumst congue dignissim interdum','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
			['testimonial_id' => '32','name' => 'Kimmy Willow','email' => 'steev.smeeth@gmail.com','image' => '/w/i/wine_tst-photo3.jpg','website' => '','company' => '','address' => '','testimonial' => 'Amet porttitor suscipit fusce elementum dignissim sed vulputate condimentum dictum nulla aliquam senectus ultrices auctor consequat letius nibh','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],


    ['testimonial_id' => '37','name' => 'Albert Einstein','email' => 'AlbertEinstein@mail.com','image' => '/t/s/ts-photo1.jpg','website' => '','company' => 'World traveler','address' => '','testimonial' => 'Every time I see an adult on a bicycle, I no longer despair for the future of the human race.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
    ['testimonial_id' => '38','name' => 'William Saroyan','email' => 'williamSaroyan@mail.com','image' => '/t/s/ts-photo2.jpg','website' => '','company' => 'World traveler','address' => '','testimonial' => 'Cyclists see considerably more of this beautiful world than any other class of citizens.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
    ['testimonial_id' => '39','name' => 'Paul Kimmage','email' => 'paulKimmage@mail.com','image' => '/t/s/ts-photo3.jpg','website' => '','company' => 'World traveler','address' => '','testimonial' => 'Life is like riding a bicycle. To keep your balance, you must keep moving.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' => $current_date_time,'updated_date' => $current_date_time],
    ['testimonial_id' => '41','name' => 'Emily Clarke','email' => 'emilyClarke@mail.com','image' => '/t/s/ts-photo4.jpg','website' => '','company' => 'World traveler','address' => '','testimonial' => 'Riding is not just a journey, it’s a way to discover your soul with every mile.','status' => '2','stores' => $storeIds["store_ids_str"],'ext_video' => '0','video_url' => '','rating' => '5','enabled_home' => '1','enabled_widget' => '1','inserted_date' =>$current_date_time,'updated_date' => $current_date_time]
		];


        if (count($magebees_customer_testimonials) > 0) {
            $this->connection->query(
                "DELETE FROM " .
                    $this->resource->getTableName(
                        "magebees_customer_testimonials"
                    )
            );
            $this->connection->insertMultiple(
                $this->resource->getTableName("magebees_customer_testimonials"),
                $magebees_customer_testimonials
            );
            $output->writeln(
                "<info>Magebees_Testimonial Sample Data Insterted Successfully.</info>"
            );
        }
        /*  testimonial end */

        $pocoBaseHelper->generateDynamicCssMenu();
		$pocoBaseHelper->generateDynamicCssBanner();

		$helper = ObjectManager::getInstance()->create(
            Data::class
        );
        $output->writeln(
            "<info>Magebees Poco Themes Sample Data Install Successfully.</info>"
        );
        return $exitCode;
    }
}
