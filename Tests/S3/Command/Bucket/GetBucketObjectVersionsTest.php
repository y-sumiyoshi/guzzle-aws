<?php
/**
 * @package Guzzle PHP <http://www.guzzlephp.org>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Aws\Tests\S3\Command\Bucket;

use Guzzle\Aws\S3\Command\Bucket\GetBucketObjectVersions;

/**
 * @author Michael Dowling <michael@guzzlephp.org>
 */
class GetBucketObjectVersionsTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @covers Guzzle\Aws\S3\Command\Bucket\GetBucketObjectVersions
     */
    public function testGetVersions()
    {
        $command = new GetBucketObjectVersions();
        $this->assertSame($command, $command->setBucket('bucket'));
        $this->assertSame($command, $command->setDelimiter('abc'));
        $this->assertSame($command, $command->setKeyMarker('3'));
        $this->assertSame($command, $command->setMaxKeys(10));
        $this->assertSame($command, $command->setPrefix('my'));
        $this->assertSame($command, $command->setVersionIdMarker(123));
        
        $client = $this->getServiceBuilder()->get('test.s3');
        $this->setMockResponse($client, 's3/GetBucketObjectVersionsResponse');
        $client->execute($command);

        $request = (string)$command->getRequest();
        $this->assertContains('GET /?versions&delimiter=abc&key-marker=3&max-keys=10&prefix=my&version-id-marker=123 HTTP/1.1', $request);
        $this->assertEquals('bucket.s3.amazonaws.com', $command->getRequest()->getHost());
    }
}