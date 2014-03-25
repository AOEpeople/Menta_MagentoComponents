<?php

/**
 * Roles
 *
 * @author Fabrizio Branca
 * @since 2014-03-24
 */
class MagentoComponents_Pages_Admin_Roles extends MagentoComponents_Pages_Admin {

    /**
     * Get roles url
     *
     * @return string
     */
    public function getRolesUrl() {
        return $this->getAdminUrl() . '/permissions_role/';
    }

    /**
     * Use filter to search for a give role
     *
     * @param $role
     */
    public function searchRoleName($role) {
        $this->getHelperCommon()->type('id=roleGrid_filter_role_name', $role . \WebDriver\Key::ENTER);
    }

    /**
     * Get grid rows
     *
     * @param $tableId (table id, NOT grid id)
     * @return array
     */
    public function getGridRows($tableId) {
        $rowData = $this->getHelperCommon()->getElements("css=#$tableId tbody tr");

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
     * @param $tableId
     * @param $value
     * @param $column
     * @return bool
     */
    public function assertGridContainsValueInColumn($tableId, $value, $column) {
        foreach ($this->getGridRows($tableId) as $row) {
            if (isset($row[$column]) && $row[$column] == $value) {
                return true;
            }
        }
        $this->getTest()->fail(sprintf('Could not find value "%s" in column "%s" in grid "%s"', $value, $column, $tableId));
    }

    /**
     * Assert role present
     *
     * @param string $role
     * @return void
     */
    public function assertRolesPresent($role)
    {
        $this->getHelperCommon()->open($this->getRolesUrl());

        $this->searchRoleName($role);
        sleep(1); // TODO: that's not nice!
        $this->assertGridContainsValueInColumn('roleGrid_table', $role, 'field_1');

    }

}
