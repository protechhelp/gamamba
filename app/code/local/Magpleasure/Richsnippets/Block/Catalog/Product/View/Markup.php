<?php

/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE.txt
 *
 * @category   Magpleasure
 * @package    Magpleasure_Richsnippets
 * @copyright  Copyright (c) 2014-2015 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE.txt
 */
class Magpleasure_Richsnippets_Block_Catalog_Product_View_Markup extends Mage_Catalog_Block_Product_View
{
    const CACHE_TAG = 'MP_RS_MARKUP';

    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addData(array(
            'cache_lifetime' => 3600,
            'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG . "_" . $this->getProduct()->getId()),
            'cache_key' => self::CACHE_TAG . '_' . Mage::app()->getStore()->getCode() . '_' . $this->getProduct()->getId(),
        ));
    }

    /**
     * Rich snippets helper
     *
     * @return Magpleasure_Richsnippets_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('richsnippets');
    }

    /**
     * Get Review Summary Model for product
     *
     * @return Mage_Review_Model_Review_Summary
     */
    protected function _getReviewSummary()
    {
        /** @var Mage_Review_Model_Review_Summary $summaryData */
        $summaryData = Mage::getModel('review/review_summary')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($this->getProduct()->getId());
        return $summaryData;
    }

    /**
     * Get product average rating value
     *
     * @return int
     */
    public function getAverageRating()
    {
        if ($this->getProduct()->getRatingSummary()) {
            return $this->getProduct()->getRatingSummary()->getRatingSummary();
        } else {
            /** @var Mage_Review_Model_Review_Summary $summaryData */
            $summaryData = $this->_getReviewSummary();
            return $summaryData->getId() ? $summaryData->getRatingSummary() : 0;
        }
    }

    /**
     * Get product reviews count
     *
     * @return int
     */
    public function getReviewsCount()
    {
        if ($this->getProduct()->getRatingSummary()) {
            $count = $this->getProduct()->getRatingSummary()->getReviewsCount();
            return $count ? $count : 0;
        } else {
            /** @var Mage_Review_Model_Review_Summary $summaryData */
            $summaryData = $this->_getReviewSummary();
            return $summaryData->getId() ? $summaryData->getReviewsCount() : 0;
        }
    }

    /**
     * Get all product reviews
     *
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    public function getReviews()
    {
        $reviews = Mage::getModel('review/review')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
            ->addEntityFilter('product', $this->getProduct()->getId())
            ->setDateOrder();
        return $reviews;
    }

    /**
     * Get average review rating
     *
     * @param $review
     * @return float|int
     */
    public function getReviewRating($review)
    {
        $votes = array();
        foreach ($review->getRatingVotes() as $vote) {
            array_push($votes, $vote->getPercent());
        }
        return count($votes) ? array_sum($votes) / count($votes) : 0;
    }
}