<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Frontend\ShoppingController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\Frontend\BkashController;
use App\Http\Controllers\Frontend\ShurjopayControllers;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderCategoryController;
use App\Http\Controllers\Admin\AssetcategoryController;
use App\Http\Controllers\Admin\AssetsubcategoryController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ChildcategoryController;
use App\Http\Controllers\Admin\ExpenseCategoriesController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\PixelsController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ApiIntegrationController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\BannerCategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CreatePageController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\CustomerManageController;
use App\Http\Controllers\Admin\ShippingChargeController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\TagManagerController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseCategoryController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\ExpenseSubcategoryController;

Auth::routes();

Route::get('/cc', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Cleared!";
});

Route::get('/migrate', function () {
    Artisan::call('migrate');
});

Route::group(['namespace' => 'Frontend', 'middleware' => ['ipcheck', 'check_refer']], function () {
    Route::get('/', [FrontendController::class, 'index'])->name('home');
    Route::get('category/{category}', [FrontendController::class, 'category'])->name('category');
    Route::get('subcategory/{subcategory}', [FrontendController::class, 'subcategory'])->name('subcategory');
    Route::get('products/{slug}', [FrontendController::class, 'products'])->name('products');
    Route::get('hot-deals', [FrontendController::class, 'hotdeals'])->name('hotdeals');
    Route::get('livesearch', [FrontendController::class, 'livesearch'])->name('livesearch');
    Route::get('search', [FrontendController::class, 'search'])->name('search');
    Route::get('product/{id}', [FrontendController::class, 'details'])->name('product');
    Route::get('quick-view', [FrontendController::class, 'quickview'])->name('quickview');
    Route::get('/shipping-charge', [FrontendController::class, 'shipping_charge'])->name('shipping.charge');
    Route::get('site/contact-us', [FrontendController::class, 'contact'])->name('contact');
    Route::get('/page/{slug}', [FrontendController::class, 'page'])->name('page');
    Route::get('districts', [FrontendController::class, 'districts'])->name('districts');
    Route::get('/campaign/{slug}', [FrontendController::class, 'campaign'])->name('campaign');
    Route::get('/offer', [FrontendController::class, 'offers'])->name('offers');
    Route::get('/payment-success', [FrontEndController::class, 'payment_success'])->name('payment_success');
    Route::get('/payment-cancel', [FrontEndController::class, 'payment_cancel'])->name('payment_cancel');

    // cart route
    Route::post('cart/store', [ShoppingController::class, 'cart_store'])->name('cart.store');
    Route::get('/add-to-cart/{id}/{qty}', [ShoppingController::class, 'addTocartGet']);
    Route::get('shop/cart', [ShoppingController::class, 'cart_show'])->name('cart.show');
    Route::get('cart/remove', [ShoppingController::class, 'cart_remove'])->name('cart.remove');
    Route::get('cart/count', [ShoppingController::class, 'cart_count'])->name('cart.count');
    Route::get('mobilecart/count', [ShoppingController::class, 'mobilecart_qty'])->name('mobile.cart.count');
    Route::get('cart/decrement', [ShoppingController::class, 'cart_decrement'])->name('cart.decrement');
    Route::get('cart/increment', [ShoppingController::class, 'cart_increment'])->name('cart.increment');
    Route::get('cart/remove-bn', [ShoppingController::class, 'cart_remove_bn'])->name('cart.remove_bn');
    Route::get('cart/decrement-bn', [ShoppingController::class, 'cart_decrement_bn'])->name('cart.decrement_bn');
    Route::get('cart/increment-bn', [ShoppingController::class, 'cart_increment_bn'])->name('cart.increment_bn');
});

Route::group(['prefix' => 'customer', 'namespace' => 'Frontend', 'middleware' => ['ipcheck', 'check_refer']], function () {
    Route::get('/login', [CustomerController::class, 'login'])->name('customer.login');
    Route::post('/signin', [CustomerController::class, 'signin'])->name('customer.signin');
    Route::get('/register', [CustomerController::class, 'register'])->name('customer.register');
    Route::post('/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/verify', [CustomerController::class, 'verify'])->name('customer.verify');
    Route::post('/verify-account', [CustomerController::class, 'account_verify'])->name('customer.account.verify');
    Route::post('/resend-otp', [CustomerController::class, 'resendotp'])->name('customer.resendotp');
    Route::post('/logout', [CustomerController::class, 'logout'])->name('customer.logout');
    Route::post('/post/review', [CustomerController::class, 'review'])->name('customer.review');
    Route::get('/forgot-password', [CustomerController::class, 'forgot_password'])->name('customer.forgot.password');
    Route::post('/forgot-verify', [CustomerController::class, 'forgot_verify'])->name('customer.forgot.verify');
    Route::get('/forgot-password/reset', [CustomerController::class, 'forgot_reset'])->name('customer.forgot.reset');
    Route::post('/forgot-password/store', [CustomerController::class, 'forgot_store'])->name('customer.forgot.store');
    Route::post('/forgot-password/resendotp', [CustomerController::class, 'forgot_resend'])->name('customer.forgot.resendotp');
    Route::get('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::post('/order-save', [CustomerController::class, 'order_save'])->name('customer.ordersave');
    Route::get('/order-success/{id}', [CustomerController::class, 'order_success'])->name('customer.order_success');
    Route::get('/order-track', [CustomerController::class, 'order_track'])->name('customer.order_track');
    Route::get('/order-track/result', [CustomerController::class, 'order_track_result'])->name('customer.order_track_result');
    Route::post('/complaint-submit', [CustomerController::class, 'complaint_submit'])->name('complaint.submit');
});
// customer auth
Route::group(['prefix' => 'customer', 'namespace' => 'Frontend', 'middleware' => ['customer', 'ipcheck', 'check_refer']], function () {
    Route::get('/account', [CustomerController::class, 'account'])->name('customer.account');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::get('/invoice', [CustomerController::class, 'invoice'])->name('customer.invoice');
    Route::get('/invoice/order-note', [CustomerController::class, 'order_note'])->name('customer.order_note');
    Route::get('/profile-edit', [CustomerController::class, 'profile_edit'])->name('customer.profile_edit');
    Route::post('/profile-update', [CustomerController::class, 'profile_update'])->name('customer.profile_update');
    Route::get('/change-password', [CustomerController::class, 'change_pass'])->name('customer.change_pass');
    Route::post('/password-update', [CustomerController::class, 'password_update'])->name('customer.password_update');
});

Route::group(['namespace' => 'Frontend', 'middleware' => ['ipcheck', 'check_refer']], function () {
    Route::get('bkash/checkout-url/pay', [BkashController::class, 'pay'])->name('url-pay');
    Route::any('bkash/checkout-url/create', [BkashController::class, 'create'])->name('url-create');
    Route::get('bkash/checkout-url/callback', [BkashController::class, 'callback'])->name('url-callback');
    Route::get('/payment-success', [ShurjopayControllers::class, 'payment_success'])->name('payment_success');
    Route::get('/payment-cancel', [ShurjopayControllers::class, 'payment_cancel'])->name('payment_cancel');
});

// unathenticate admin route
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['customer', 'ipcheck', 'check_refer']], function () {
    Route::get('locked', [DashboardController::class, 'locked'])->name('locked');
    Route::post('unlocked', [DashboardController::class, 'unlocked'])->name('unlocked');
});

// ajax route
Route::get('/ajax-product-subcategory', [ProductController::class, 'getSubcategory']);
Route::get('/ajax-product-childcategory', [ProductController::class, 'getChildcategory']);
// backend routes
Route::get('/ajax-expense-subcategory', [ExpenseController::class, 'getSubcategory']);
Route::get('/ajax-asset-subcategory', [AssetController::class, 'getSubcategory']);

// auth route
Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'lock', 'check_refer'], 'prefix' => 'admin'], function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('change-password', [DashboardController::class, 'changepassword'])->name('change_password');
    Route::post('new-password', [DashboardController::class, 'newpassword'])->name('new_password');

    // users route
    Route::get('users/manage', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users/save', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('users/update', [UserController::class, 'update'])->name('users.update');
    Route::post('users/inactive', [UserController::class, 'inactive'])->name('users.inactive');
    Route::post('users/active', [UserController::class, 'active'])->name('users.active');
    Route::post('users/destroy', [UserController::class, 'destroy'])->name('users.destroy');

    // roles
    Route::get('roles/manage', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/{id}/show', [RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles/save', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('roles/update', [RoleController::class, 'update'])->name('roles.update');
    Route::post('roles/destroy', [RoleController::class, 'destroy'])->name('roles.destroy');

    // permissions
    Route::get('permissions/manage', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('permissions/{id}/show', [PermissionController::class, 'show'])->name('permissions.show');
    Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('permissions/save', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('permissions/update', [PermissionController::class, 'update'])->name('permissions.update');
    Route::post('permissions/destroy', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    // categories
    Route::get('categories/manage', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{id}/show', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories/save', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('categories/update', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('categories/inactive', [CategoryController::class, 'inactive'])->name('categories.inactive');
    Route::post('categories/active', [CategoryController::class, 'active'])->name('categories.active');
    Route::post('categories/destroy', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Subcategories
    Route::get('subcategories/manage', [SubcategoryController::class, 'index'])->name('subcategories.index');
    Route::get('subcategories/{id}/show', [SubcategoryController::class, 'show'])->name('subcategories.show');
    Route::get('subcategories/create', [SubcategoryController::class, 'create'])->name('subcategories.create');
    Route::post('subcategories/save', [SubcategoryController::class, 'store'])->name('subcategories.store');
    Route::get('subcategories/{id}/edit', [SubcategoryController::class, 'edit'])->name('subcategories.edit');
    Route::post('subcategories/update', [SubcategoryController::class, 'update'])->name('subcategories.update');
    Route::post('subcategories/inactive', [SubcategoryController::class, 'inactive'])->name('subcategories.inactive');
    Route::post('subcategories/active', [SubcategoryController::class, 'active'])->name('subcategories.active');
    Route::post('subcategories/destroy', [SubcategoryController::class, 'destroy'])->name('subcategories.destroy');

    // Childcategories
    Route::get('childcategories/manage', [ChildcategoryController::class, 'index'])->name('childcategories.index');
    Route::get('childcategories/{id}/show', [ChildcategoryController::class, 'show'])->name('childcategories.show');
    Route::get('childcategories/create', [ChildcategoryController::class, 'create'])->name('childcategories.create');
    Route::post('childcategories/save', [ChildcategoryController::class, 'store'])->name('childcategories.store');
    Route::get('childcategories/{id}/edit', [ChildcategoryController::class, 'edit'])->name('childcategories.edit');
    Route::post('childcategories/update', [ChildcategoryController::class, 'update'])->name('childcategories.update');
    Route::post('childcategories/inactive', [ChildcategoryController::class, 'inactive'])->name('childcategories.inactive');
    Route::post('childcategories/active', [ChildcategoryController::class, 'active'])->name('childcategories.active');
    Route::post('childcategories/destroy', [ChildcategoryController::class, 'destroy'])->name('childcategories.destroy');

    // paymentgeteway
    Route::get('paymentgeteway/manage', [ApiIntegrationController::class, 'pay_manage'])->name('paymentgeteway.manage');
    Route::post('paymentgeteway/save', [ApiIntegrationController::class, 'pay_update'])->name('paymentgeteway.update');

    // smsgeteway
    Route::get('smsgeteway/manage', [ApiIntegrationController::class, 'sms_manage'])->name('smsgeteway.manage');
    Route::post('smsgeteway/save', [ApiIntegrationController::class, 'sms_update'])->name('smsgeteway.update');

    // courierapi
    Route::get('courierapi/manage', [ApiIntegrationController::class, 'courier_manage'])->name('courierapi.manage');
    Route::post('courierapi/save', [ApiIntegrationController::class, 'courier_update'])->name('courierapi.update');

    // attribute
    Route::get('orderstatus/manage', [OrderStatusController::class, 'index'])->name('orderstatus.index');
    Route::get('orderstatus/{id}/show', [OrderStatusController::class, 'show'])->name('orderstatus.show');
    Route::get('orderstatus/create', [OrderStatusController::class, 'create'])->name('orderstatus.create');
    Route::post('orderstatus/save', [OrderStatusController::class, 'store'])->name('orderstatus.store');
    Route::get('orderstatus/{id}/edit', [OrderStatusController::class, 'edit'])->name('orderstatus.edit');
    Route::post('orderstatus/update', [OrderStatusController::class, 'update'])->name('orderstatus.update');
    Route::post('orderstatus/inactive', [OrderStatusController::class, 'inactive'])->name('orderstatus.inactive');
    Route::post('orderstatus/active', [OrderStatusController::class, 'active'])->name('orderstatus.active');
    Route::post('orderstatus/destroy', [OrderStatusController::class, 'destroy'])->name('orderstatus.destroy');

    // pixels
    Route::get('pixels/manage', [PixelsController::class, 'index'])->name('pixels.index');
    Route::get('pixels/{id}/show', [PixelsController::class, 'show'])->name('pixels.show');
    Route::get('pixels/create', [PixelsController::class, 'create'])->name('pixels.create');
    Route::post('pixels/save', [PixelsController::class, 'store'])->name('pixels.store');
    Route::get('pixels/{id}/edit', [PixelsController::class, 'edit'])->name('pixels.edit');
    Route::post('pixels/update', [PixelsController::class, 'update'])->name('pixels.update');
    Route::post('pixels/inactive', [PixelsController::class, 'inactive'])->name('pixels.inactive');
    Route::post('pixels/active', [PixelsController::class, 'active'])->name('pixels.active');
    Route::post('pixels/destroy', [PixelsController::class, 'destroy'])->name('pixels.destroy');

    // tag manager
    Route::get('tag-manager/manage', [TagManagerController::class, 'index'])->name('tagmanagers.index');
    Route::get('tag-manager/{id}/show', [TagManagerController::class, 'show'])->name('tagmanagers.show');
    Route::get('tag-manager/create', [TagManagerController::class, 'create'])->name('tagmanagers.create');
    Route::post('tag-manager/save', [TagManagerController::class, 'store'])->name('tagmanagers.store');
    Route::get('tag-manager/{id}/edit', [TagManagerController::class, 'edit'])->name('tagmanagers.edit');
    Route::post('tag-manager/update', [TagManagerController::class, 'update'])->name('tagmanagers.update');
    Route::post('tag-manager/inactive', [TagManagerController::class, 'inactive'])->name('tagmanagers.inactive');
    Route::post('tag-manager/active', [TagManagerController::class, 'active'])->name('tagmanagers.active');
    Route::post('tag-manager/destroy', [TagManagerController::class, 'destroy'])->name('tagmanagers.destroy');

    // attribute
    Route::get('brands/manage', [BrandController::class, 'index'])->name('brands.index');
    Route::get('brands/{id}/show', [BrandController::class, 'show'])->name('brands.show');
    Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('brands/save', [BrandController::class, 'store'])->name('brands.store');
    Route::get('brands/{id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::post('brands/update', [BrandController::class, 'update'])->name('brands.update');
    Route::post('brands/inactive', [BrandController::class, 'inactive'])->name('brands.inactive');
    Route::post('brands/active', [BrandController::class, 'active'])->name('brands.active');
    Route::post('brands/destroy', [BrandController::class, 'destroy'])->name('brands.destroy');

    // color
    Route::get('color/manage', [ColorController::class, 'index'])->name('colors.index');
    Route::get('color/{id}/show', [ColorController::class, 'show'])->name('colors.show');
    Route::get('color/create', [ColorController::class, 'create'])->name('colors.create');
    Route::post('color/save', [ColorController::class, 'store'])->name('colors.store');
    Route::get('color/{id}/edit', [ColorController::class, 'edit'])->name('colors.edit');
    Route::post('color/update', [ColorController::class, 'update'])->name('colors.update');
    Route::post('color/inactive', [ColorController::class, 'inactive'])->name('colors.inactive');
    Route::post('color/active', [ColorController::class, 'active'])->name('colors.active');
    Route::post('color/destroy', [ColorController::class, 'destroy'])->name('colors.destroy');

    // size
    Route::get('size/manage', [SizeController::class, 'index'])->name('sizes.index');
    Route::get('size/{id}/show', [SizeController::class, 'show'])->name('sizes.show');
    Route::get('size/create', [SizeController::class, 'create'])->name('sizes.create');
    Route::post('size/save', [SizeController::class, 'store'])->name('sizes.store');
    Route::get('size/{id}/edit', [SizeController::class, 'edit'])->name('sizes.edit');
    Route::post('size/update', [SizeController::class, 'update'])->name('sizes.update');
    Route::post('size/inactive', [SizeController::class, 'inactive'])->name('sizes.inactive');
    Route::post('size/active', [SizeController::class, 'active'])->name('sizes.active');
    Route::post('size/destroy', [SizeController::class, 'destroy'])->name('sizes.destroy');


    Route::get('supplier/manage', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
    Route::post('supplier/save', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('supplier/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::post('supplier/update', [SupplierController::class, 'update'])->name('supplier.update');
    Route::post('supplier/inactive', [SupplierController::class, 'inactive'])->name('supplier.inactive');
    Route::post('supplier/active', [SupplierController::class, 'active'])->name('supplier.active');
    Route::post('supplier/destroy', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    Route::get('supplier/profile', [SupplierController::class, 'profile'])->name('supplier.profile');

    // purchase categories
    Route::get('purchase-categories/manage', [PurchaseCategoryController::class, 'index'])->name('purchase.categories.index');
    Route::get('purchase-categories/{id}/show', [PurchaseCategoryController::class, 'show'])->name('purchase.categories.show');
    Route::get('purchase-categories/create', [PurchaseCategoryController::class, 'create'])->name('purchase.categories.create');
    Route::post('purchase-categories/save', [PurchaseCategoryController::class, 'store'])->name('purchase.categories.store');
    Route::get('purchase-categories/{id}/edit', [PurchaseCategoryController::class, 'edit'])->name('purchase.categories.edit');
    Route::post('purchase-categories/update', [PurchaseCategoryController::class, 'update'])->name('purchase.categories.update');
    Route::post('purchase-categories/inactive', [PurchaseCategoryController::class, 'inactive'])->name('purchase.categories.inactive');
    Route::post('purchase-categories/active', [PurchaseCategoryController::class, 'active'])->name('purchase.categories.active');
    Route::post('purchase-categories/destroy', [PurchaseCategoryController::class, 'destroy'])->name('purchase.categories.destroy');

    // purchase controller route
    Route::get('purchase/manage', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::get('purchase/create', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::get('purchase/cart-add', [PurchaseController::class, 'cart_add'])->name('purchase.add');
    Route::get('purchase/cart-content', [PurchaseController::class, 'cart_content'])->name('purchase.cart_content');
    Route::get('purchase/cart-increment', [PurchaseController::class, 'cart_increment'])->name('purchase.cart_increment');
    Route::get('purchase/cart-decrement', [PurchaseController::class, 'cart_decrement'])->name('purchase.cart_decrement');
    Route::get('purchase/cart-remove', [PurchaseController::class, 'cart_remove'])->name('purchase.cart_remove');
    Route::get('purchase/cart-product-discount', [PurchaseController::class, 'product_discount'])->name('purchase.product_discount');
    Route::get('purchase/cart-product-quantity', [PurchaseController::class, 'product_quantity'])->name('purchase.product_quantity');
    Route::get('purchase/cart-details', [PurchaseController::class, 'cart_details'])->name('purchase.cart_details');
    Route::get('purchase/cart-shipping', [PurchaseController::class, 'cart_shipping'])->name('purchase.cart_shipping');
    Route::get('purchase/cart-clear', [PurchaseController::class, 'cart_clear'])->name('purchase.cart_clear');
    Route::get('purchase/paid', [PurchaseController::class, 'purchase_paid'])->name('purchase.paid');
    Route::post('purchase/store', [PurchaseController::class, 'purchase_store'])->name('purchase.store');
    Route::get('purchase/edit/{id}', [PurchaseController::class, 'purchase_edit'])->name('purchase.edit');
    Route::post('purchase/update', [PurchaseController::class, 'purchase_update'])->name('purchase.update');
    Route::get('purchase/invoice/{invoice_id}', [PurchaseController::class, 'invoice'])->name('purchase.invoice');

    Route::get('purchase-summary', [PurchaseController::class, 'purchase_summary'])->name('purchase.summary');
    Route::get('purchase-details', [PurchaseController::class, 'purchase_details'])->name('purchase.details');
    Route::get('purchase-details', [PurchaseController::class, 'purchase_details'])->name('purchase.details');
    Route::get('supplier-ledger', [PurchaseController::class, 'supplier_ledger'])->name('admin.sledger_report');
    Route::get('purchase/select/warehouse', [PurchaseController::class, 'purchase_select_warehouse'])->name('purchase.warehouse.select');

    // collection
    Route::get('payment/index', [PaymentController::class, 'index'])->name('admin.payment.index');
    Route::get('payment/create', [PaymentController::class, 'create'])->name('admin.payment.create');
    Route::post('payment/store', [PaymentController::class, 'store'])->name('admin.payment.store');
    Route::get('payment/edit/{id}', [PaymentController::class, 'edit'])->name('admin.payment.edit');
    Route::post('payment/update', [PaymentController::class, 'update'])->name('admin.payment.update');
    Route::post('payment/destroy', [PaymentController::class, 'destroy'])->name('admin.payment.destroy');
    Route::get('user-select', [PaymentController::class, 'user_select'])->name('admin.user.select');


    // collection
    Route::get('collection/index', [CollectionController::class, 'index'])->name('admin.collection.index');
    Route::get('collection/create', [CollectionController::class, 'create'])->name('admin.collection.create');
    Route::post('collection/store', [CollectionController::class, 'store'])->name('admin.collection.store');
    Route::get('collection/edit/{id}', [CollectionController::class, 'edit'])->name('admin.collection.edit');
    Route::post('collection/update', [CollectionController::class, 'update'])->name('admin.collection.update');
    Route::post('collection/destroy', [CollectionController::class, 'destroy'])->name('admin.collection.destroy');

    // product
    Route::get('products/manage', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{id}/show', [ProductController::class, 'show'])->name('products.show');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/save', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('products/update', [ProductController::class, 'update'])->name('products.update');
    Route::post('products/inactive', [ProductController::class, 'inactive'])->name('products.inactive');
    Route::post('products/active', [ProductController::class, 'active'])->name('products.active');
    Route::post('products/destroy', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('products/image/destroy', [ProductController::class, 'imgdestroy'])->name('products.image.destroy');
    Route::get('products/update-deals', [ProductController::class, 'update_deals'])->name('products.update_deals');
    Route::get('products/update-feature', [ProductController::class, 'update_feature'])->name('products.update_feature');
    Route::get('products/update-status', [ProductController::class, 'update_status'])->name('products.update_status');
    Route::get('products/price-edit', [ProductController::class, 'price_edit'])->name('products.price_edit');
    Route::post('products/price-update', [ProductController::class, 'price_update'])->name('products.price_update');

    // campaign
    Route::get('campaign/manage', [CampaignController::class, 'index'])->name('campaign.index');
    Route::get('campaign/{id}/show', [CampaignController::class, 'show'])->name('campaign.show');
    Route::get('campaign/create', [CampaignController::class, 'create'])->name('campaign.create');
    Route::post('campaign/save', [CampaignController::class, 'store'])->name('campaign.store');
    Route::get('campaign/{id}/edit', [CampaignController::class, 'edit'])->name('campaign.edit');
    Route::post('campaign/update', [CampaignController::class, 'update'])->name('campaign.update');
    Route::post('campaign/inactive', [CampaignController::class, 'inactive'])->name('campaign.inactive');
    Route::post('campaign/active', [CampaignController::class, 'active'])->name('campaign.active');
    Route::post('campaign/destroy', [CampaignController::class, 'destroy'])->name('campaign.destroy');
    Route::get('campaign/image/destroy', [CampaignController::class, 'imgdestroy'])->name('campaign.image.destroy');

    // settings route
    Route::get('settings/manage', [GeneralSettingController::class, 'index'])->name('settings.index');
    Route::get('settings/create', [GeneralSettingController::class, 'create'])->name('settings.create');
    Route::post('settings/save', [GeneralSettingController::class, 'store'])->name('settings.store');
    Route::get('settings/{id}/edit', [GeneralSettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings/update', [GeneralSettingController::class, 'update'])->name('settings.update');
    Route::post('settings/inactive', [GeneralSettingController::class, 'inactive'])->name('settings.inactive');
    Route::post('settings/active', [GeneralSettingController::class, 'active'])->name('settings.active');
    Route::post('settings/destroy', [GeneralSettingController::class, 'destroy'])->name('settings.destroy');

    // settings route
    Route::get('social-media/manage', [SocialMediaController::class, 'index'])->name('socialmedias.index');
    Route::get('social-media/create', [SocialMediaController::class, 'create'])->name('socialmedias.create');
    Route::post('social-media/save', [SocialMediaController::class, 'store'])->name('socialmedias.store');
    Route::get('social-media/{id}/edit', [SocialMediaController::class, 'edit'])->name('socialmedias.edit');
    Route::post('social-media/update', [SocialMediaController::class, 'update'])->name('socialmedias.update');
    Route::post('social-media/inactive', [SocialMediaController::class, 'inactive'])->name('socialmedias.inactive');
    Route::post('social-media/active', [SocialMediaController::class, 'active'])->name('socialmedias.active');
    Route::post('social-media/destroy', [SocialMediaController::class, 'destroy'])->name('socialmedias.destroy');

    // contact route
    Route::get('contact/manage', [ContactController::class, 'index'])->name('contact.index');
    Route::get('contact/create', [ContactController::class, 'create'])->name('contact.create');
    Route::post('contact/save', [ContactController::class, 'store'])->name('contact.store');
    Route::get('contact/{id}/edit', [ContactController::class, 'edit'])->name('contact.edit');
    Route::post('contact/update', [ContactController::class, 'update'])->name('contact.update');
    Route::post('contact/inactive', [ContactController::class, 'inactive'])->name('contact.inactive');
    Route::post('contact/active', [ContactController::class, 'active'])->name('contact.active');
    Route::post('contact/destroy', [ContactController::class, 'destroy'])->name('contact.destroy');

    // banner category route
    Route::get('banner-category/manage', [BannerCategoryController::class, 'index'])->name('banner_category.index');
    Route::get('banner-category/create', [BannerCategoryController::class, 'create'])->name('banner_category.create');
    Route::post('banner-category/save', [BannerCategoryController::class, 'store'])->name('banner_category.store');
    Route::get('banner-category/{id}/edit', [BannerCategoryController::class, 'edit'])->name('banner_category.edit');
    Route::post('banner-category/update', [BannerCategoryController::class, 'update'])->name('banner_category.update');
    Route::post('banner-category/inactive', [BannerCategoryController::class, 'inactive'])->name('banner_category.inactive');
    Route::post('banner-category/active', [BannerCategoryController::class, 'active'])->name('banner_category.active');
    Route::post('banner-category/destroy', [BannerCategoryController::class, 'destroy'])->name('banner_category.destroy');

    // banner  route
    Route::get('banner/manage', [BannerController::class, 'index'])->name('banners.index');
    Route::get('banner/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('banner/save', [BannerController::class, 'store'])->name('banners.store');
    Route::get('banner/{id}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::post('banner/update', [BannerController::class, 'update'])->name('banners.update');
    Route::post('banner/inactive', [BannerController::class, 'inactive'])->name('banners.inactive');
    Route::post('banner/active', [BannerController::class, 'active'])->name('banners.active');
    Route::post('banner/destroy', [BannerController::class, 'destroy'])->name('banners.destroy');

    // expensecategories
    Route::get('expensecategories/manage', [ExpenseCategoriesController::class, 'index'])->name('expensecategories.index');
    Route::get('expensecategories/{id}/show', [ExpenseCategoriesController::class, 'show'])->name('expensecategories.show');
    Route::get('expensecategories/create', [ExpenseCategoriesController::class, 'create'])->name('expensecategories.create');
    Route::post('expensecategories/save', [ExpenseCategoriesController::class, 'store'])->name('expensecategories.store');
    Route::get('expensecategories/{id}/edit', [ExpenseCategoriesController::class, 'edit'])->name('expensecategories.edit');
    Route::post('expensecategories/update', [ExpenseCategoriesController::class, 'update'])->name('expensecategories.update');
    Route::post('expensecategories/inactive', [ExpenseCategoriesController::class, 'inactive'])->name('expensecategories.inactive');
    Route::post('expensecategories/active', [ExpenseCategoriesController::class, 'active'])->name('expensecategories.active');
    Route::post('expensecategories/destroy', [ExpenseCategoriesController::class, 'destroy'])->name('expensecategories.destroy');

    // expensesubcategories
    Route::get('expense-subcategories/manage', [ExpenseSubcategoryController::class, 'index'])->name('expensesubcategories.index');
    Route::get('expense-subcategories/{id}/show', [ExpenseSubcategoryController::class, 'show'])->name('expensesubcategories.show');
    Route::get('expense-subcategories/create', [ExpenseSubcategoryController::class, 'create'])->name('expensesubcategories.create');
    Route::post('expense-subcategories/save', [ExpenseSubcategoryController::class, 'store'])->name('expensesubcategories.store');
    Route::get('expense-subcategories/{id}/edit', [ExpenseSubcategoryController::class, 'edit'])->name('expensesubcategories.edit');
    Route::post('expense-subcategories/update', [ExpenseSubcategoryController::class, 'update'])->name('expensesubcategories.update');
    Route::post('expense-subcategories/inactive', [ExpenseSubcategoryController::class, 'inactive'])->name('expensesubcategories.inactive');
    Route::post('expense-subcategories/active', [ExpenseSubcategoryController::class, 'active'])->name('expensesubcategories.active');
    Route::post('expense-subcategories/destroy', [ExpenseSubcategoryController::class, 'destroy'])->name('expensesubcategories.destroy');

    // expense
    Route::get('expense/manage', [ExpenseController::class, 'index'])->name('expense.index');
    Route::get('expense/{id}/show', [ExpenseController::class, 'show'])->name('expense.show');
    Route::get('expense/create', [ExpenseController::class, 'create'])->name('expense.create');
    Route::post('expense/save', [ExpenseController::class, 'store'])->name('expense.store');
    Route::get('expense/{id}/edit', [ExpenseController::class, 'edit'])->name('expense.edit');
    Route::post('expense/update', [ExpenseController::class, 'update'])->name('expense.update');
    Route::post('expense/inactive', [ExpenseController::class, 'inactive'])->name('expense.inactive');
    Route::post('expense/active', [ExpenseController::class, 'active'])->name('expense.active');
    Route::post('expense/destroy', [ExpenseController::class, 'destroy'])->name('expense.destroy');



    // contact route
    Route::get('page/manage', [CreatePageController::class, 'index'])->name('pages.index');
    Route::get('page/create', [CreatePageController::class, 'create'])->name('pages.create');
    Route::post('page/save', [CreatePageController::class, 'store'])->name('pages.store');
    Route::get('page/{id}/edit', [CreatePageController::class, 'edit'])->name('pages.edit');
    Route::post('page/update', [CreatePageController::class, 'update'])->name('pages.update');
    Route::post('page/inactive', [CreatePageController::class, 'inactive'])->name('pages.inactive');
    Route::post('page/active', [CreatePageController::class, 'active'])->name('pages.active');
    Route::post('page/destroy', [CreatePageController::class, 'destroy'])->name('pages.destroy');

 // purchase categories
    Route::get('order-categories/manage', [OrderCategoryController::class, 'index'])->name('order.categories.index');
    Route::get('order-categories/{id}/show', [OrderCategoryController::class, 'show'])->name('order.categories.show');
    Route::get('order-categories/create', [OrderCategoryController::class, 'create'])->name('order.categories.create');
    Route::post('order-categories/save', [OrderCategoryController::class, 'store'])->name('order.categories.store');
    Route::get('order-categories/{id}/edit', [OrderCategoryController::class, 'edit'])->name('order.categories.edit');
    Route::post('order-categories/update', [OrderCategoryController::class, 'update'])->name('order.categories.update');
    Route::post('order-categories/inactive', [OrderCategoryController::class, 'inactive'])->name('order.categories.inactive');
    Route::post('order-categories/active', [OrderCategoryController::class, 'active'])->name('order.categories.active');
    Route::post('order-categories/destroy', [OrderCategoryController::class, 'destroy'])->name('order.categories.destroy');

    // asset categories
    Route::get('asset-categories/manage', [AssetcategoryController::class, 'index'])->name('asset.categories.index');
    Route::get('asset-categories/{id}/show', [AssetcategoryController::class, 'show'])->name('asset.categories.show');
    Route::get('asset-categories/create', [AssetcategoryController::class, 'create'])->name('asset.categories.create');
    Route::post('asset-categories/save', [AssetcategoryController::class, 'store'])->name('asset.categories.store');
    Route::get('asset-categories/{id}/edit', [AssetcategoryController::class, 'edit'])->name('asset.categories.edit');
    Route::post('asset-categories/update', [AssetcategoryController::class, 'update'])->name('asset.categories.update');
    Route::post('asset-categories/inactive', [AssetcategoryController::class, 'inactive'])->name('asset.categories.inactive');
    Route::post('asset-categories/active', [AssetcategoryController::class, 'active'])->name('asset.categories.active');
    Route::post('asset-categories/destroy', [AssetcategoryController::class, 'destroy'])->name('asset.categories.destroy');

    // asset subcategories
    Route::get('asset-subcategories/manage', [AssetsubcategoryController::class, 'index'])->name('asset.subcategories.index');
    Route::get('asset-subcategories/{id}/show', [AssetsubcategoryController::class, 'show'])->name('asset.subcategories.show');
    Route::get('asset-subcategories/create', [AssetsubcategoryController::class, 'create'])->name('asset.subcategories.create');
    Route::post('asset-subcategories/save', [AssetsubcategoryController::class, 'store'])->name('asset.subcategories.store');
    Route::get('asset-subcategories/{id}/edit', [AssetsubcategoryController::class, 'edit'])->name('asset.subcategories.edit');
    Route::post('asset-subcategories/update', [AssetsubcategoryController::class, 'update'])->name('asset.subcategories.update');
    Route::post('asset-subcategories/inactive', [AssetsubcategoryController::class, 'inactive'])->name('asset.subcategories.inactive');
    Route::post('asset-subcategories/active', [AssetsubcategoryController::class, 'active'])->name('asset.subcategories.active');
    Route::post('asset-subcategories/destroy', [AssetsubcategoryController::class, 'destroy'])->name('asset.subcategories.destroy');

    // asset categories
    Route::get('asset/manage', [AssetController::class, 'index'])->name('asset.index');
    Route::get('asset/{id}/show', [AssetController::class, 'show'])->name('asset.show');
    Route::get('asset/create', [AssetController::class, 'create'])->name('asset.create');
    Route::post('asset/save', [AssetController::class, 'store'])->name('asset.store');
    Route::get('asset/{id}/edit', [AssetController::class, 'edit'])->name('asset.edit');
    Route::post('asset/update', [AssetController::class, 'update'])->name('asset.update');
    Route::post('asset/inactive', [AssetController::class, 'inactive'])->name('asset.inactive');
    Route::post('asset/active', [AssetController::class, 'active'])->name('asset.active');
    Route::post('asset/destroy', [AssetController::class, 'destroy'])->name('asset.destroy');

    // Pos route
    Route::get('order/search', [OrderController::class, 'search'])->name('admin.livesearch');
    Route::get('order/create', [OrderController::class, 'order_create'])->name('admin.order.create');
    Route::post('order/store', [OrderController::class, 'order_store'])->name('admin.order.store');
    Route::get('order/cart-add', [OrderController::class, 'cart_add'])->name('admin.order.cart_add');
    Route::get('order/cart-content', [OrderController::class, 'cart_content'])->name('admin.order.cart_content');
    Route::get('order/cart-increment', [OrderController::class, 'cart_increment'])->name('admin.order.cart_increment');
    Route::get('order/cart-decrement', [OrderController::class, 'cart_decrement'])->name('admin.order.cart_decrement');
    Route::get('order/cart-remove', [OrderController::class, 'cart_remove'])->name('admin.order.cart_remove');
    Route::get('order/cart-product-discount', [OrderController::class, 'product_discount'])->name('admin.order.product_discount');
    Route::get('order/cart-product-quantity', [OrderController::class, 'product_quantity'])->name('admin.order.product_quantity');
    Route::get('order/cart-details', [OrderController::class, 'cart_details'])->name('admin.order.cart_details');
    Route::get('order/cart-shipping', [OrderController::class, 'cart_shipping'])->name('admin.order.cart_shipping');
    Route::post('order/cart-clear', [OrderController::class, 'cart_clear'])->name('admin.order.cart_clear');
    Route::get('order/paid', [OrderController::class, 'order_paid'])->name('admin.order.paid');
    Route::get('order/additional-shipping', [OrderController::class, 'additional_shipping'])->name('admin.order.additional_shipping');
    Route::get('order/select/warehouse', [OrderController::class, 'order_select_warehouse'])->name('order.warehouse.select');

    // Order route
    Route::get('office/orders', [OrderController::class, 'office_orders'])->name('admin.office.orders');
    Route::get('order/{slug}', [OrderController::class, 'index'])->name('admin.orders');
    Route::get('order/edit/{invoice_id}', [OrderController::class, 'order_edit'])->name('admin.order.edit');
    Route::post('order/update', [OrderController::class, 'order_update'])->name('admin.order.update');
    Route::get('order/invoice/{invoice_id}', [OrderController::class, 'invoice'])->name('admin.order.invoice');
    Route::get('order/process/{invoice_id}', [OrderController::class, 'process'])->name('admin.order.process');
    Route::post('order/change', [OrderController::class, 'order_process'])->name('admin.order_change');
    Route::post('order/item-return', [OrderController::class, 'order_return'])->name('admin.order.item_return');
    Route::post('order/item-replace', [OrderController::class, 'order_replace'])->name('admin.order.item_replace');
    Route::post('order/destroy', [OrderController::class, 'destroy'])->name('admin.order.destroy');
    Route::get('order-assign', [OrderController::class, 'order_assign'])->name('admin.order.assign');
    Route::get('order-status', [OrderController::class, 'order_status'])->name('admin.order.status');
    Route::get('order-bulk-destroy', [OrderController::class, 'bulk_destroy'])->name('admin.order.bulk_destroy');
    Route::get('order-print', [OrderController::class, 'order_print'])->name('admin.order.order_print');
    Route::get('bulk-courier/{slug}', [OrderController::class, 'bulk_courier'])->name('admin.bulk_courier');
    Route::get('order-pathao', [OrderController::class, 'order_pathao'])->name('admin.order.pathao');
    Route::get('pathao-city', [OrderController::class, 'pathaocity'])->name('pathaocity');
    Route::get('pathao-zone', [OrderController::class, 'pathaozone'])->name('pathaozone');
    Route::get('stock-report', [OrderController::class, 'stock_report'])->name('admin.stock_report');
    Route::get('warehouse-report', [OrderController::class, 'warehouse_report'])->name('admin.warehouse_report');
    Route::get('order-report', [OrderController::class, 'order_report'])->name('admin.order_report');
    Route::get('return-report', [OrderController::class, 'return_report'])->name('admin.return_report');
    Route::get('replace-report', [OrderController::class, 'replace_report'])->name('admin.replace_report');
    Route::get('expense-report', [OrderController::class, 'expense_report'])->name('admin.expense_report');
    Route::get('asset-report', [OrderController::class, 'asset_report'])->name('admin.asset_report');
    Route::get('loss-profit', [OrderController::class, 'loss_profit'])->name('admin.loss_profit');
    Route::get('customer-select', [OrderController::class, 'customer_select'])->name('admin.customer.select');

    // Order route
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('review/pending', [ReviewController::class, 'pending'])->name('reviews.pending');
    Route::post('review/inactive', [ReviewController::class, 'inactive'])->name('reviews.inactive');
    Route::post('review/active', [ReviewController::class, 'active'])->name('reviews.active');
    Route::get('review/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('review/save', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('review/{id}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::post('review/update', [ReviewController::class, 'update'])->name('reviews.update');
    Route::post('review/destroy', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // flavor  route
    Route::get('shipping-charge/manage', [ShippingChargeController::class, 'index'])->name('shippingcharges.index');
    Route::get('shipping-charge/create', [ShippingChargeController::class, 'create'])->name('shippingcharges.create');
    Route::post('shipping-charge/save', [ShippingChargeController::class, 'store'])->name('shippingcharges.store');
    Route::get('shipping-charge/{id}/edit', [ShippingChargeController::class, 'edit'])->name('shippingcharges.edit');
    Route::post('shipping-charge/update', [ShippingChargeController::class, 'update'])->name('shippingcharges.update');
    Route::post('shipping-charge/inactive', [ShippingChargeController::class, 'inactive'])->name('shippingcharges.inactive');
    Route::post('shipping-charge/active', [ShippingChargeController::class, 'active'])->name('shippingcharges.active');
    Route::post('shipping-charge/destroy', [ShippingChargeController::class, 'destroy'])->name('shippingcharges.destroy');

    // backend customer route
    Route::get('customer/create', [CustomerManageController::class, 'create'])->name('customers.create');
    Route::post('customer/save', [CustomerManageController::class, 'store'])->name('customers.store');
    Route::get('customer/manage', [CustomerManageController::class, 'index'])->name('customers.index');
    Route::get('customer/{id}/edit', [CustomerManageController::class, 'edit'])->name('customers.edit');
    Route::post('customer/update', [CustomerManageController::class, 'update'])->name('customers.update');
    Route::post('customer/inactive', [CustomerManageController::class, 'inactive'])->name('customers.inactive');
    Route::post('customer/active', [CustomerManageController::class, 'active'])->name('customers.active');
    Route::get('customer/profile', [CustomerManageController::class, 'profile'])->name('customers.profile');
    Route::post('customer/adminlog', [CustomerManageController::class, 'adminlog'])->name('customers.adminlog');
    Route::get('customer/ip-block', [CustomerManageController::class, 'ip_block'])->name('customers.ip_block');
    Route::post('customer/ip-store', [CustomerManageController::class, 'ipblock_store'])->name('customers.ipblock.store');
    Route::post('customer/ip-update', [CustomerManageController::class, 'ipblock_update'])->name('customers.ipblock.update');
    Route::post('customer/ip-destroy', [CustomerManageController::class, 'ipblock_destroy'])->name('customers.ipblock.destroy');
    Route::get('customer/complaints', [CustomerManageController::class, 'complaints'])->name('customers.complaint');
    Route::get('customer/complaint/{id}', [CustomerManageController::class, 'complaint_view'])->name('customers.complaint.view');


    Route::get('purchase-reports', [ReportsController::class, 'purchase_reports'])->name('purchase.reports');
    Route::get('cash-purchase', [ReportsController::class, 'cash_purchase'])->name('purchase.cash_reports');
    Route::get('due-purchase', [ReportsController::class, 'due_purchase'])->name('purchase.due_reports');
    Route::get('purchase-details', [ReportsController::class, 'purchase_details'])->name('purchase.details');
    Route::get('due-paid', [ReportsController::class, 'due_paid'])->name('admin.due_paid');
    Route::get('supplier-ledger', [ReportsController::class, 'supplier_ledger'])->name('admin.sledger_report');
    Route::get('cash-sales', [ReportsController::class, 'cash_sales'])->name('order.cash_sales');
    Route::get('due-sales', [ReportsController::class, 'due_sales'])->name('order.due_sales');
    Route::get('offer_sales', [ReportsController::class, 'offer_sales'])->name('admin.offer_sales');

    // warehouse controller
    Route::get('warehouse/manage', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('warehouse/{id}/show', [WarehouseController::class, 'show'])->name('warehouses.show');
    Route::get('warehouse/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    Route::post('warehouse/save', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('warehouse/{id}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
    Route::post('warehouse/update', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::post('warehouse/stock-change', [WarehouseController::class, 'stock_change'])->name('warehouses.stock_change');
    Route::post('warehouse/inactive', [WarehouseController::class, 'inactive'])->name('warehouses.inactive');
    Route::post('warehouse/active', [WarehouseController::class, 'active'])->name('warehouses.active');
    Route::post('warehouse/destroy', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');
    Route::get('warehouse/profile', [WarehouseController::class, 'profile'])->name('warehouses.profile');

    Route::get('warehouse/transfers', [WarehouseController::class, 'transfers'])->name('warehouses.transfers');

});
