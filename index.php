<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=danildicoding;AccountKey=3sdAuj6Y/H26ZrksJ3fIUJsGyOsbEhoyY91O0VPpwpCdcSl68mSOs13qhClDHJQA4cgEX9ACzKHaJgl7xqJ1AQ==";
$containerName = "danildicodingcontainer";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);


if (isset($_POST['submit'])) {
    $fileToUpload = $_FILES['fileToUpload']['name'];
    $content = fopen($_FILES['fileToUpload']['tmp_name'].'', "r");
    $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
}
?>

<!DOCTYPE html>
<html>
<body>

<div>
	<form action="index.php" method="post" enctype="multipart/form-data">
	    Select image to upload:
	    <input type="file" name="fileToUpload" id="fileToUpload" accept="image/x-png,image/jpeg">
	    <input type="submit" value="Upload Image" name="submit">
	</form>
</div>

<div style="margin-top: 30px">
    <?php
        // List blobs.
        $listBlobsOptions = new ListBlobsOptions();

        echo "These are the blobs present in the container: ";

        do{
            $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
            foreach ($result->getBlobs() as $blob)
            {
                ?>
                    <div style="margin-top: 20px">
                        <img src="<?php echo $blob->getUrl() ?>" width="150px">
                        <b><?= $blob->getName() ?></b>
                        <form action="computervision.php" method="post">
                            <input type="hidden" name="data" value="<?= $blob->getUrl() ?>">
                            <input type="submit" name="submit" value="Analyze Image">
                        </form>
                        <hr>
                    </div>
                <?php
            }

            $listBlobsOptions->setContinuationToken($result->getContinuationToken());
        } while($result->getContinuationToken());
        echo "<br />";
    ?>
</div>

</body>
</html>