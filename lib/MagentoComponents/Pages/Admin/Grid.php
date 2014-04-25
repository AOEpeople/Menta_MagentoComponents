<?php

/**
 * Admin grid component
 *
 * @author Fabrizio Branca
 * @since 2014-04-25
 */
class MagentoComponents_Pages_Admin_Grid extends MagentoComponents_Pages_Admin
{

    /**
     * Get grid rows
     *
     * @param $gridId (grid id, NOT table id)
     * @return array
     */
    public function getGridRows($gridId) {
        $rowData = $this->getHelperCommon()->getElements("css=#{$gridId}_table tbody tr");

        $data = array();
        foreach ($rowData as $row) { /* @var $row WebDriver\Element */
            $cellData = $this->getHelperCommon()->getElements('css=td', $row);

            $tmp = array('title' => $row->attribute('title'));
            foreach ($cellData as $i => $cell) { /* @var $cell WebDriver\Element */
                $tmp['field_'.$i] = $this->getHelperCommon()->getText($cell);
            }
            $data[] = $tmp;
        }
        return $data;
    }

    /**
     * Assert grid contains value in a given column
     *
     * @param $gridId
     * @param $value
     * @param $column
     * @return bool
     */
    public function assertGridContainsValueInColumn($gridId, $value, $column) {
        foreach ($this->getGridRows($gridId) as $row) {
            if (isset($row[$column]) && $row[$column] == $value) {
                return true;
            }
        }
        $this->getTest()->fail(sprintf('Could not find value "%s" in column "%s" in grid "%s"', $value, $column, $gridId));
    }

    /**
     * Return the total number of rows in the grid
     * (Grid might be filtered)
     *
     * @param $gridId
     * @return bool
     */
    public function getTotalNumberOfRows($gridId) {
        $text = $this->getHelperCommon()->getElement("css=#{$gridId} table.actions td.pager")->text();
        $matches = array();
        if (preg_match('/Total ([0-9]+) records found/', $text, $matches)) {
            return $matches[1];
        } else {
            return false;
        }
    }

    /**
     * Assert total number of rows
     * (Grid might be filtered)
     *
     * @param $gridId
     * @param $expectedNumberOfRows
     */
    public function assertTotalNumberOfRows($gridId, $expectedNumberOfRows) {
        $this->getTest()->assertEquals($expectedNumberOfRows, $this->getTotalNumberOfRows($gridId));
    }

}
