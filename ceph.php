<?php

    define('AWS_KEY', 'W3545FTHIXC1QF7IWD3M');
    define('AWS_SECRET_KEY', 'RfcO23aN2vi4OQjPzeAxqC18diYjEwHUbbz755NZ');

    require './vendor/autoload.php';

    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;

    try {
		// You may need to change the region. It will say in the URL when the bucket is open
		// and on creation. us-east-2 is Ohio, us-east-1 is North Virgina
		$s3 = S3Client::factory(
			array(
				'credentials' => array(
					'key' => AWS_KEY,
					'secret' => AWS_SECRET_KEY
				),
				'version' => 'latest',
                'region'  => 'us-east-1',
                'use_path_style_endpoint' => true,
                'endpoint' => 'https://ceph.apps.pm.pb.gov.br'
			)
        );
        
        $objects = $s3->listObjects([
            'Bucket' => 'sasp'
        ]);

        foreach ($objects['Contents'] as $object) {
            echo $object['Key'] . PHP_EOL;
        }

        die();
    } 
    catch (Exception $e) {
		// We use a die, so if this fails. It stops here. Typically this is a REST call so this would
		// return a json object.
		die("Error: " . $e->getMessage());
    }
    
 ?>
