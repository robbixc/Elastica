<?php
namespace Elastica\Test\Query;

use Elastica\Document;
use Elastica\Query\SpanTerm;
use Elastica\Test\Base as BaseTest;

class SpanTermTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $field = 'name';
        $value = 'marek';
        $query = new SpanTerm([$field => $value]);

        $expectedArray = [
            'span_term' => [
                $field => $value,
            ],
        ];

        $this->assertEquals($expectedArray, $query->toArray());
    }

    /**
     * @group functional
     */
    public function testSpanTerm()
    {
        $field = 'name';
        $value = 'match';

        $index = $this->_createIndex();
        $type = $index->getType('test');

        $docMisData = [$field => 'mismatch', 'email' => 'test2@test.com'];
        $docHitData = [$field => $value, 'email' => 'test@test.com'];

        $doc1 = new Document(1, $docMisData);
        $doc2 = new Document(2, $docHitData);
        $type->addDocuments([$doc1, $doc2]);
        $index->refresh();

        $query = new SpanTerm([$field => $value]);
        $resultSet = $type->search($query);
        $results = $resultSet->getResults();
        $hitData = reset($results)->getData();

        $this->assertEquals($docHitData, $hitData);
    }
}
