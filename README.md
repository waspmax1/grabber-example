# E-shop parser example
## Using:
1) Guzzle
2) PhpQuery
3) PhpExcel
4) RegExp
### Usage:
### Copy links file into 'links' folder
#### Create index.php with code:
    ini_set('max_execution_time', 7200);
    set_time_limit(7200);
    
    require_once 'vendor/autoload.php';
    
    use app\ProductPage;
    use app\GetProductData;
    
    $productPage = new ProductPage();
    $productData = new GetProductData();
    
    $productPageData = $productPage->getProductPage('linksFile.txt', '127.0.0.1:8080');
    $parsedProduct = $productData->parseProduct($productPageData);
    $productData->saveProduct('folderName', $parsedProduct);
    
