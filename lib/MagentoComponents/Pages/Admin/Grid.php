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

        $columnTh = $this->getHelperCommon()->getElements("css=#{$gridId}_table thead tr.headings th");

        $rowData = $this->getHelperCommon()->getElements("css=#{$gridId}_table tbody tr");

        $data = array();
        foreach ($rowData as $row) { /* @var $row WebDriver\Element */
            $tmp = array('title' => $row->attribute('title'));

            $cellData = $this->getHelperCommon()->getElements('css=td', $row);
            foreach ($cellData as $i => $cell) { /* @var $cell WebDriver\Element */
                $tmp[trim($columnTh[$i]->text())] = $tmp['field_'.$i] = $this->getHelperCommon()->getText($cell);
                $tmp['element_'.$i] = $cell;
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
     * Get row url
     * (Magento stores this in the title attribute)
     *
     * @param $gridId
     * @param $rowIndex
     * @return mixed
     */
    public function getRowUrl($gridId, $rowIndex) {
        $rows = $this->getGridRows($gridId);
        return $rows[$rowIndex]['title'];
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

    /**
     * Filter field
     *
     * @param $field
     * @param $from
     * @param $to
     * @param bool $submit
     */
    public function filterFromTo($field, $from, $to, $submit=true) {
        $this->getHelperCommon()->type("id={$field}_from", $from);
        $this->getHelperCommon()->type("id={$field}_to", $to);
        if ($submit) {
            $this->getHelperCommon()->getElement('//button[@title="Search"]')->click();
        }
    }

}
