<?php
namespace Magebees\PocoThemes\Console\Command;

class PromotionsnotificationData {
	public static function getPromotionsnotification($current_date_time,$to_date){
		$magebees_promotionsnotification = [
			['notification_id' => '1','title' => 'Poco Theme Electronic Store','notification_content' => '<p><span style="color: #282c3d; font-weight: 700; font-size: 15px; line-height: normal;">GET 30% TO 50% OFF ON SELECTED ITEMS</span> </p>','notification_style' => 'bar','background_color' => '#E7FD40','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'poco-theme-electronic-store'],
			['notification_id' => '2','title' => 'Poco Theme Fashion Version 1','notification_content' => '<p><span style="color: #737384; font-weight: 500;">UK\'S CHOICE OF FASHION SALE </span> <a style="color: #4d3ad2; text-decoration: underline;" href="#"><strong style="font-weight: 600;">GET 30% OFF</strong></a></p>','notification_style' => 'bar','background_color' => '#F9F3EE','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'poco-theme-fashion-version-1'],
			['notification_id' => '3','title' => 'Fashion Popup Notification','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/fashion/fashion-01.jpg"}}" alt="" height="460" width="820" /></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#F9F3EE','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'fashion-popup-notification'],
			['notification_id' => '4','title' => 'Cosmetic Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/cosmetic/cosmetic.jpg"}}" alt="" height="460" width="820" /></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#E0E0E0','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'cosmetic-newsletter-popup'],
			['notification_id' => '5','title' => 'Electronic Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/electronic/electronics.jpg"}}" alt="" height="460" width="820"  /></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'electronic-newsletter-popup'],
			['notification_id' => '6','title' => 'Autoparts Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/autoparts/auto-part.jpg"}}" alt="" height="460" width="820"/></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'autoparts-newsletter-popup'],
			['notification_id' => '7','title' => 'Furniture Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url=&quot;wysiwyg/furniture/furniture.jpg&quot;}}" alt="Newsletter" width="820" height="460"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#EDEBEE','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'furniture-newsletter-popup'],
			['notification_id' => '8','title' => 'Medical Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/medical/medical.jpg"}}" alt=""  width="820px" height="460px"/></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#E1E8FB','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'medical-newsletter-popup'],
			['notification_id' => '9','title' => 'Poco Theme Fashion Version 2','notification_content' => '<p><span style="color: #6f6f6f; font-weight: 600;letter-spacing: 0.4px;">UK\'S CHOICE OF FASHION SALE </span> <a href="#" style="color: #232323; text-decoration: none;"><b style="font-weight: 600;">GET 70% OFF</b></a></p>','notification_style' => 'bar','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'poco-theme-fashion-version-2'],
			['notification_id' => '10','title' => 'Fashion2 Popup Notification','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url=&quot;wysiwyg/fashion/fashion-02.jpg&quot;}}" alt="" width="820" height="460"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#F9F3EE','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'fashion2-popup-notification'],
			['notification_id' => '11','title' => 'Kids Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url=&quot;wysiwyg/kids/kids.jpg&quot;}}" alt="" width="820" height="460"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#FFAFA7','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'kids-newsletter-popup'],
			['notification_id' => '12','title' => 'Poco Theme Decor Version 1','notification_content' => '<div class="marquee-section">   
			<ul class="marquee-items d-flex align-item-center">
				<li class="marquee-item f-item">                        
					<div class="marquee-info">Free shipping for all orders from $50+</div>
				</li>
				<li class="marquee-item f-item">   
					<div class="marquee-info">Need help: +36 0123 456 789</div>
				</li>
				<li class="marquee-item f-item">
					<div class="marquee-info">30% off everything (yes, everything!)</div>
				</li>
				<li class="marquee-item f-item">                        
					<div class="marquee-info"> Get an extra 25% off using the code 10stella</div>
				</li>
				<li class="marquee-item f-item">                        
					<div class="marquee-info">Our favorites products</div>
				</li>
				<li class="marquee-item f-item">                        
					<div class="marquee-info">Free shipping for all orders from $50+</div>
				</li>
				<li class="marquee-item f-item">   
					<div class="marquee-info">Need help: +36 0123 456 789</div>
				</li>
			   <li class="marquee-item f-item">
					<div class="marquee-info">30% off everything (yes, everything!)</div>
				</li>
				<li class="marquee-item f-item">                        
					<div class="marquee-info"> Get an extra 25% off using the code 10stella</div>
				</li>
				<li class="marquee-item f-item">                        
					<div class="marquee-info">Our favorites products</div>
				</li>
			</ul>      
			</div>','notification_style' => 'bar','background_color' => '#CDC05A','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'poco-theme-decor-version-1'],
			['notification_id' => '13','title' => 'Decor Popup Notification','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/decor/newsletter_popup.jpg"}}" alt="Newsletter" width="820" height="460"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'decor-popup-notification'],
			['notification_id' => '14','title' => 'Footwear Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/footwear/newsletter_popup.jpg"}}" alt="Newsletter" width="820" height="460"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'footwear-newsletter-popup'],
			['notification_id' => '15','title' => 'Jewellery Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/jewellery/newsletter_popup.jpg"}}" alt="Newsletter" width="820" height="460"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'jewellery-newsletter-popup'],
			['notification_id' => '16','title' => 'Poco Theme Jewellery Store','notification_content' => '<div class="marquee-section">   
			<div class="marquee-itm d-flex alc">       
				<div class="marquee-itemss">
					<div class="marquee-item f-item">                        
						<div class="marquee-card d_fl alc">
							<div class="marquee-icon"><img src="{{media url="wysiwyg/jewellery/star-icon.png"}}" alt="" width="9" height="9" /></div>
							<div class="marquee-info"><p>Join our showroom and <a href="#">Get 30% off</a></p></div>
						</div>
					</div>
					<div class="marquee-item f-item">                        
						<div class="marquee-card d_fl alc">
							<div class="marquee-icon"><img src="{{media url="wysiwyg/jewellery/star-icon.png"}}" alt="" width="9" height="9" /></div>
							<div class="marquee-info"><p>Free shipping for order above $500.00. <a href="#">Shop Now</a></p></div>
						</div>
					</div>
					<div class="marquee-item f-item">                        
						<div class="marquee-card d_fl alc">
							<div class="marquee-icon"><img src="{{media url="wysiwyg/jewellery/star-icon.png"}}" alt="" width="9" height="9" /></div>
							<div class="marquee-info"><p>Order online promocode: <a href="#">Stella10</a></p></div>
						</div>
					</div>
					<div class="marquee-item f-item">                        
						<div class="marquee-card d_fl alc">
							<div class="marquee-icon"><img src="{{media url="wysiwyg/jewellery/star-icon.png"}}" alt="" width="9" height="9" /></div>
							<div class="marquee-info"><p>Short content about your store</p></div>
						</div>
					</div>
				</div>
				<div class="marquee-itemss">
					<div class="marquee-item f-item">                        
						<div class="marquee-card d_fl alc">
							<div class="marquee-icon"><img src="{{media url="wysiwyg/jewellery/star-icon.png"}}" alt="" width="9" height="9" /></div>
							<div class="marquee-info"><p>Join our showroom and <a href="#">Get 30% off</a></p></div>
						</div>
					</div>
					<div class="marquee-item f-item">                        
						<div class="marquee-card d_fl alc">
							<div class="marquee-icon"><img src="{{media url="wysiwyg/jewellery/star-icon.png"}}" alt="" width="9" height="9" /></div>
							<div class="marquee-info"><p>Free shipping for order above $500.00. <a href="#">Shop Now</a></p></div>
						</div>
					</div>
					<div class="marquee-item f-item">                        
						<div class="marquee-card d_fl alc">
							<div class="marquee-icon"><img src="{{media url="wysiwyg/jewellery/star-icon.png"}}" alt="" width="9" height="9" /></div>
							<div class="marquee-info"><p>Order online promocode: <a href="#">Stella10</a></p></div>
						</div>
					</div>
					<div class="marquee-item f-item">                        
						<div class="marquee-card d_fl alc">
							<div class="marquee-icon"><img src="{{media url="wysiwyg/jewellery/star-icon.png"}}" alt="" width="9" height="9" /></div>
							<div class="marquee-info"><p>Short content about your store</p></div>
						</div>
					</div>
				</div>
			</div>
			</div>','notification_style' => 'bar','background_color' => '#242424','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'poco-theme-jewellery-store'],
			['notification_id' => '17','title' => 'Vitamin Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/vitamins/newsletter_popup.jpg"}}" alt="Newsletter" width="820" height="460"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'vitamin-newsletter-popup'],
			['notification_id' => '18','title' => 'Guns Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url=&quot;wysiwyg/guns/nws_pop.jpg&quot;}}" alt="Newsletter" width="820" height="460"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'guns-newsletter-popup'],
			['notification_id' => '19','title' => 'Gaming Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url="wysiwyg/game/nws_pup_img.jpg"}}" alt="Newsletter" width="820" height="460"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'gaming-newsletter-popup'],
			['notification_id' => '20','title' => 'Bakery Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url=&quot;wysiwyg/bakery/newsletter_popup.jpg&quot;}}" alt="Newsletter" width="820" height="460" loading="lazy"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#F4F1EC','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'bakery-newsletter-popup'],
			['notification_id' => '21','title' => 'Wine Newsletter popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
			<div class="nws_full_bg"><img src="{{media url=&quot;wysiwyg/wine/newsletter_popup.jpg&quot;}}" alt="Newsletter" width="820" height="460" loading="lazy"></div>
			<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
			</div>','notification_style' => 'popup','background_color' => '#22222C','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'wine-newsletter-popup'],
		    ['notification_id' => '22','title' => 'Mega Store Newsletter popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
<div class="nws_full_bg"><img src="{{media url=&quot;wysiwyg/mega-store/newsletter_popup.jpg&quot;}}" alt="Newsletter" width="820" height="460" loading="lazy" /></div>
<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
</div>','notification_style' => 'popup','background_color' => '#22222C','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'mega-store-newsletter-popup'],
    ['notification_id' => '23','title' => 'Pet Store Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
<div class="nws_full_bg"><img src="{{media url=&quot;wysiwyg/pet-store/newsletter_popup.jpg&quot;}}" alt="Newsletter" width="820" height="460" loading="lazy" /></div>
<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
</div>','notification_style' => 'popup','background_color' => '#F5E2DB','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'pet-store-newsletter-popup'],
['notification_id' => '24','title' => 'Poco Theme Bike Version 1','notification_content' => '<p>Subscribe save 20% + Free delivery <a style="text-decoration: underline;" href="#"><strong style="font-weight: 600;">Subscribe now</strong></a></p>','notification_style' => 'bar','background_color' => '#B5E848','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'poco-theme-bike-version-1'],
    ['notification_id' => '25','title' => 'Bike Store Newsletter Popup','notification_content' => '<div class="newsletter-in d-flex align-items-center">
<div class="nws_full_bg"><img src="{{media url=&quot;wysiwyg/bike/newsletter_popup.jpg&quot;}}" alt="Newsletter" width="820" height="460" loading="lazy"></div>
<div class="newsletter-form flex-1">{{block class="Magento\\Newsletter\\Block\\Subscribe" template="Magebees_Promotionsnotification::popup_subscribe.phtml"}}</div>
</div>','notification_style' => 'popup','background_color' => '#FFFFFF','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'bike-store-newsletter-popup'],
    ['notification_id' => '26','title' => 'Poco Theme Bike Bar Version 1','notification_content' => '<p>New 2025 Bikes Just Landed – <a style="text-decoration: underline;" href="#"><strong style="font-weight: 600;">Explore Now!</strong></a></p>','notification_style' => 'bar','background_color' => '#B5E848','from_date' => $current_date_time,'to_date' => $to_date,'sort_order' => '0','status' => '1','cart_page' => '0','unique_code' => 'poco-theme-bike-bar-version-1']
		];
		return $magebees_promotionsnotification;
	}
	
}