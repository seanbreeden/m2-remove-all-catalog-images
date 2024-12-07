<?php

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\State;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
require __DIR__ . '/app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$appState = $objectManager->get(State::class);
$appState->setAreaCode('adminhtml');
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
$productCollectionFactory = $objectManager->get(ProductCollectionFactory::class);
$productCollection = $productCollectionFactory->create();
$productCollection->addAttributeToSelect('sku');
$productIds = $productCollection->getAllIds();
if (empty($productIds)) {
    echo "No products found." . PHP_EOL;
    exit;
}
foreach ($productIds as $productId) {
    try {
        $product = $productRepository->getById($productId, false, null, true);
        $mediaGalleryEntries = $product->getMediaGalleryEntries();
        if (!empty($mediaGalleryEntries)) {
            $product->setMediaGalleryEntries([]);
            $productRepository->save($product);
            echo "Removed images for product ID: {$productId} ({$product->getSku()})" . PHP_EOL;
        } else {
            echo "No images found for product ID: {$productId} ({$product->getSku()})" . PHP_EOL;
        }
    } catch (\Exception $e) {
        echo "Error for product ID: {$productId}: " . $e->getMessage() . PHP_EOL;
    }
}
echo "All product images have been processed." . PHP_EOL;
