<?php
/**
 * @package Guzzle PHP <http://www.guzzlephp.org>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Aws\Tests\S3\Command\Object;

use Guzzle\Guzzle;
use Guzzle\Aws\S3\S3Client;
use Guzzle\Aws\S3\Command\Object\PutObject;

/**
 * @author Michael Dowling <michael@guzzlephp.org>
 */
class PutObjectTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @covers Guzzle\Aws\S3\Command\Object\PutObject
     * @covers Guzzle\Aws\S3\Command\Object\AbstractRequestObject
     * @covers Guzzle\Aws\S3\Command\Object\AbstractRequestObjectPut
     */
    public function testPutObject()
    {
        $command = new PutObject();
        $command->setBucket('test')->setKey('key');
        $command->setBody('data');

        $command->getRequestHeaders()->set('x-amz-test', '123');
        $command->setAcl(S3Client::ACL_PUBLIC_READ);
        $command->setStorageClass('STANDARD');

        $client = $this->getServiceBuilder()->get('test.s3');
        $this->setMockResponse($client, 's3/PutObjectResponse');
        $client->execute($command);

        $request = (string) $command->getRequest();
        $this->assertEquals('http://test.s3.amazonaws.com/key', $command->getRequest()->getUrl());
        $this->assertEquals('PUT', $command->getRequest()->getMethod());

        $this->assertFalse($this->compareHttpHeaders(array(
            'Host' => 'test.s3.amazonaws.com',
            'Date' => '*',
            'Content-Length' => '4',
            'Content-MD5' =>  'jXd/OF09/siBXSD3SWAm3A==',
            'Authorization' => '*',
            'x-amz-test' => '123',
            'x-amz-acl' => 'public-read',
            'x-amz-storage-class' => 'STANDARD',
            'User-Agent' => Guzzle::getDefaultUserAgent(),
            'Expect' => '100-Continue'
        ), $command->prepare()->getHeaders()->getAll()));

        $this->assertEquals('data', (string) $command->getRequest()->getBody());
    }

    /**
     * @covers Guzzle\Aws\S3\Command\Object\PutObject
     */
    public function testPutObjectNoChecksum()
    {
        $command = new PutObject();
        $command->setBucket('test')->setKey('key')->setBody('data');

        $command->disableChecksumValidation();

        $client = $this->getServiceBuilder()->get('test.s3');
        $this->setMockResponse($client, 's3/PutObjectResponse');
        $client->execute($command);

        $this->assertFalse($command->getRequestHeaders()->hasKey('Content-MD5'));
    }

    /**
     * @covers Guzzle\Aws\S3\Command\Object\AbstractRequestObjectPut::setStorageClass
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage $storageClass must be one of STANDARD or REDUCED_REDUNDANCY
     */
    public function testValidatesStorageClass()
    {
        $command = new PutObject();
        $command->setStorageClass('error');
    }
}