<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsNews\Model\Tables;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Sql\Expression;
use MelisEngine\Model\Tables\MelisGenericTable;

class MelisCmsNewsTable extends MelisGenericTable
{

    /**
     * Table name
     */
    const TABLE = 'melis_cms_news';
    /**
     * Primary key
     */
    const PRIMARY_KEY = 'cnews_id';

    protected $cnews_text_cols = [
        'cnews_text_id',
        'cnews_title',
        'cnews_subtitle',
        'cnews_paragraph1',
        'cnews_paragraph2',
        'cnews_paragraph3',
        'cnews_paragraph4',
        'cnews_paragraph5',
        'cnews_paragraph6',
        'cnews_paragraph7',
        'cnews_paragraph8',
        'cnews_paragraph9',
        'cnews_paragraph10',
        'cnews_id',
        'cnews_lang_id',
    ];

    /**
     * MelisCmsNewsTable constructor.
     */
    public function __construct()
    {
        $this->idField = self::PRIMARY_KEY;
    }

    /**
     * @param $newsId
     * @param null $langId
     * @return mixed
     */
    public function getNews($newsId, $langId = null)
    {
        $select = $this->tableGateway->getSql()->select();
        $clause = array();

        $select->join('melis_cms_site', 'melis_cms_site.site_id = melis_cms_news.cnews_site_id', array('site_name'), $select::JOIN_LEFT);
        $select->join('melis_cms_news_texts', 'melis_cms_news_texts.cnews_id = melis_cms_news.cnews_id', '*', $select::JOIN_LEFT);

        $select->where('melis_cms_news.cnews_id =' . (int)$newsId);

        if (!is_null($langId)) {
            $select->where('melis_cms_news_texts.cnews_lang_id =' . (int)$langId);
        }

        $resultData = $this->tableGateway->selectWith($select);
        return $resultData;
    }

    public function getNewsByIdArray(array $newsIdArray, $langId = null, array $where = [])
    {
        $select = $this->tableGateway->getSql()->select();

        $select->join('melis_cms_site', 'melis_cms_site.site_id = melis_cms_news.cnews_site_id', array('site_name'), $select::JOIN_LEFT);
        $select->join('melis_cms_news_texts', 'melis_cms_news_texts.cnews_id = melis_cms_news.cnews_id', '*', $select::JOIN_LEFT);

        $select->where(['melis_cms_news.cnews_id' => $newsIdArray]);

        if (!is_null($langId)) {
            $select->where('melis_cms_news_texts.cnews_lang_id =' . (int)$langId);
        }

        if (!is_null($where['search'])) {
            $search = '%' . addcslashes((string) $where['search'], '%_\\') . '%'; // « % » / « _ » searched as characters (Mantis #0011370)
            $select->where->NEST->like('melis_cms_news.cnews_id', $search)
                ->or->like('melis_cms_news_texts.cnews_title', $search);
        }

        $resultData = $this->tableGateway->selectWith($select);
        return $resultData;
    }

    public function getNewsList(
        $status             = null,
        $langId             = null,
        $dateMin            = null,
        $dateMax            = null,
        $publishDateMin     = null,
        $publishDateMax     = null,
        $unpublishFilter    = false,
        $start              = null,
        $limit              = null,
        $orderColumn        = null,
        $order              = null,
        $siteId             = null,
        $search             = null,
        $count              = false
    ) {
        $select = $this->tableGateway->getSql()->select();

        $select->join('melis_cms_site', 'melis_cms_site.site_id = melis_cms_news.cnews_site_id', array('site_name', 'site_label'), $select::JOIN_LEFT);
        $select->join('melis_cms_news_texts', 'melis_cms_news_texts.cnews_id = melis_cms_news.cnews_id', '*', $select::JOIN_LEFT);

        if (!is_null($search)) {
            // « % » and « _ » searched as characters, not LIKE wildcards: « % » listed every news and « A_tu »
            // matched « Actu » in the front News list / Latest news search (Mantis #0011370, as #0011287 for the blog)
            $search = '%' . addcslashes((string) $search, '%_\\') . '%';
            $select->where->NEST->like('melis_cms_news.cnews_id', $search)
                ->or->like('melis_cms_news_texts.cnews_title', $search);
        }

        if (!is_null($siteId)) {
            $select->where->equalTo('cnews_site_id', (int)$siteId);
        }

        if (!is_null($status)) {
            $select->where('cnews_status =' . (int)$status);
        }

        if (!is_null($langId)) {
            $select->where('melis_cms_news_texts.cnews_lang_id =' . (int)$langId);
        }

        if (!is_null($dateMin)) {
            $select->where->greaterThanOrEqualTo('cnews_creation_date', (string) $dateMin);
        }

        if (!is_null($dateMax)) {
            $select->where->lessThanOrEqualTo('cnews_creation_date', (string) $dateMax);
        }

        if (!is_null($publishDateMin)) {
            $select->where->greaterThanOrEqualTo(new Expression('DATE(cnews_publish_date)'), (string) $publishDateMin);
        }

        if (!is_null($publishDateMax)) {
            $select->where->lessThanOrEqualTo(new Expression('DATE(cnews_publish_date)'), (string) $publishDateMax);
        }

        if (!is_null($limit)) {
            $select->limit((int) $limit);
        }

        if ($unpublishFilter) {
            $select->where->nest->greaterThan('cnews_unpublish_date', date("Y-m-d H:i:s"))->or->isNull('cnews_unpublish_date')->unnest;
        }

        if (!is_null($start)) {
            $select->offset($start);
        }

        if (!is_null($orderColumn) && !is_null($order)) {
            if ($orderColumn == 'site_label') {
                \MelisCore\Model\Tables\MelisGenericTable::addSafeOrder($select, 'melis_cms_site.' . $orderColumn, $order);
            } elseif (in_array($orderColumn, $this->cnews_text_cols)) {
                \MelisCore\Model\Tables\MelisGenericTable::addSafeOrder($select, 'melis_cms_news_texts.' . $orderColumn, $order);
            } else {
                \MelisCore\Model\Tables\MelisGenericTable::addSafeOrder($select, 'melis_cms_news.' . $orderColumn, $order);
            }
        }

        if (!empty($count)) {
            $select->group('melis_cms_news.cnews_id');
        }

        $select->where('melis_cms_news_texts.cnews_title !=""');

        $resultData = $this->tableGateway->selectWith($select);

        return $resultData;
    }

    public function checkForDuplicates($search)
    {
        $search = '%' . $search . '%';
        $sql = "select Number From
        (select cnews_documents1 as Number from melis_cms_news WHERE cnews_documents1 LIKE ?
        union all
        select cnews_documents2 as Number from melis_cms_news WHERE cnews_documents2 LIKE ?
        union all
        select cnews_documents3 as Number from melis_cms_news WHERE cnews_documents3 LIKE ?
        union all
        select cnews_image1  as Number from melis_cms_news WHERE cnews_image1 LIKE ?
        union all
        select cnews_image2  as Number from melis_cms_news WHERE cnews_image2 LIKE ?
        union all
        select cnews_image3 as Number from melis_cms_news WHERE cnews_image3 LIKE ?
        ) myTab";

        // Parameterize the query with bound placeholders to prevent SQL injection
        $adapter = $this->tableGateway->getAdapter();
        $statement = $adapter->createStatement($sql);
        $resultData = $statement->execute([$search, $search, $search, $search, $search, $search]);
        return $resultData;
    }

    public function getNewsListByMonths($limit = null, $siteId = null)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->columns(array(
            new Expression('MONTH(cnews_publish_date) AS month'),
            new Expression('YEAR(cnews_publish_date) AS year'),
        ));
        $select->where(array('cnews_status' => '1'));

        if (!is_null($siteId)) {
            $select->where->equalTo('cnews_site_id', $siteId);
        }

        $select->where->lessThan('cnews_publish_date', date('Y-m-d H:i:s', strtotime("now")));

        $select->where->nest->greaterThan('cnews_unpublish_date', date('Y-m-d H:i:s', strtotime("now")))->or->isNull('cnews_unpublish_date')->unnest;

        $select->group(array('month', 'year'));
        $select->order(new Expression('month + " " + year'), 'DESC');


        if (!is_null($limit)) {
            $select->limit($limit);
        }


        $resultData = $this->tableGateway->selectWith($select);
        return $resultData;
    }

    public function getNewsByMonthYear($month, $year, $limit = null, $siteId = null)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->join('melis_cms_news_texts', 'melis_cms_news_texts.cnews_id = melis_cms_news.cnews_id', array('cnews_id', 'cnews_title'), $select::JOIN_LEFT);

        $select->where(array('cnews_status' => '1'));

        if (!is_null($siteId)) {
            $select->where->equalTo('cnews_site_id', (int)$siteId);
        }

        $select->where('MONTH(cnews_publish_date) = ' . (int)$month);
        $select->where('YEAR(cnews_publish_date) =' . (int)$year);
        $select->group("melis_cms_news.cnews_id");
        $select->where->nest->greaterThan('cnews_unpublish_date', date('Y-m-d H:i:s', strtotime("now")))->or->isNull('cnews_unpublish_date')->unnest;

        $select->order(array('cnews_publish_date' => 'DESC'));
        if (!is_null($limit)) {
            $select->limit($limit);
        }

        $resultData = $this->tableGateway->selectWith($select);

        return $resultData;
    }

    /**
     * get last news record
     * @return Object
     */
    public function getLastNews()
    {
        $select = $this->tableGateway->getSql()->select();

        $select->order(array('cnews_id' => 'DESC'));

        $select->limit(1);

        return $this->tableGateway->selectWith($select);
    }

    /**
     * get last news record
     * @return Object
     */
    public function getNewsData($where)
    {
        $select = $this->tableGateway->getSql()->select();

        $select->join('melis_cms_news_texts', 'melis_cms_news_texts.cnews_id = melis_cms_news.cnews_id', array('cnews_title', 'cnews_lang_id'), $select::JOIN_LEFT);

        $select->where($where);

        return $this->tableGateway->selectWith($select);
    }


    /**
     * get categories of news by news id
     * @return Object
     */
    public function getNewsCategories($newsId, $langId = 1, $where = [])
    {
        $select = $this->tableGateway->getSql()->select();
        $select->where(['cnews_id' => $newsId]);
        $select->join(
            'melis_cms_news_category',
            'melis_cms_news_category.cnc_cnews_id = melis_cms_news.cnews_id',
            ['cnc_id', 'cnc_cat2_id', 'cnc_order'],
            $select::JOIN_LEFT
        );

        $select->join(
            'melis_cms_category2',
            'melis_cms_category2.cat2_id = melis_cms_news_category.cnc_cat2_id',
            ['cat2_id'],
            $select::JOIN_LEFT
        );

        if (is_null($langId)) {
            $select->join(
                'melis_cms_category2_trans',
                'melis_cms_category2_trans.catt2_category_id = melis_cms_category2.cat2_id',
                ['catt2_name', 'catt2_description', 'catt2_lang_id'],
                $select::JOIN_LEFT
            );
        } else {
            $select->join(
                'melis_cms_category2_trans',
                'melis_cms_category2_trans.catt2_category_id = melis_cms_category2.cat2_id',
                ['catt2_name', 'catt2_description', 'catt2_lang_id'],
                $select::JOIN_LEFT
            )->where(['melis_cms_category2_trans.catt2_lang_id' => $langId]);
        }

        if (!empty($where)) {
            $select->where($where);
        }

        $select->having([new \Laminas\Db\Sql\Predicate\IsNotNull('catt2_name'), new \Laminas\Db\Sql\Predicate\Expression("catt2_name != ''")]);

        return $this->tableGateway->selectWith($select);
    }

    /**
     * Tags of a news (one row per tag), each with its title in $langId or, when that text is missing or
     * empty, the first non-empty title of another language — same rule as the React API (tagsAction).
     *
     * The old query kept only the tags having a text row in $langId: a tag named in another language only
     * was left out and one with an empty text came back nameless, so the legacy Tags field dropped it and
     * the next legacy save deleted it from the news (Mantis #0011368).
     *
     * @return \Laminas\Db\ResultSet\ResultSetInterface rows: cnews_id, id, tag_id, entity_id, entity_type, tag_title
     */
    public function getNewsTags($newsId, $langId = 1, $where = [])
    {
        $sql = "SELECT e.entity_id AS cnews_id, e.id, e.tag_id, e.entity_id, e.entity_type,
                       COALESCE(NULLIF(tx.tag_title, ''),
                           (SELECT tx2.tag_title FROM melis_cms_tag_texts tx2
                             WHERE tx2.tag_id = e.tag_id
                               AND tx2.tag_title IS NOT NULL AND tx2.tag_title <> ''
                             ORDER BY tx2.tag_lang_id LIMIT 1),
                           CONCAT('#', e.tag_id)) AS tag_title
                  FROM melis_cms_tag_entity e
                  LEFT JOIN melis_cms_tag_texts tx ON tx.tag_id = e.tag_id AND tx.tag_lang_id = ?
                 WHERE e.entity_type = 'NEWS' AND e.entity_id = ?
                 ORDER BY e.id";
        // $where is no longer used (its only caller never passed it); kept in the signature for BC.
        return $this->tableGateway->getAdapter()->query($sql, [(int) $langId, (int) $newsId]);
    }
}
