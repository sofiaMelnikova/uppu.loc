<?php

namespace Engine;


class Pagination
{
    /**
     * @param int $sumElements
     * @param int $elementsOnPage
     * @return int
     */
    public function getCountPagesOrGroups (int $sumElements, int $elementsOnPage) {

        if ($sumElements <= $elementsOnPage) {
            return 1;
        }

        if (($sumElements%$elementsOnPage) != 0) {
            return intval($sumElements/$elementsOnPage) + 1;
        }

        return $sumElements/$elementsOnPage;
    }

    /**
     * @param int $actualPage
     * @param int $elementsOnPage
     * @return array
     */
    public function getMinMaxElementsOnPage (int $actualPage, int $elementsOnPage) {
        if ($actualPage === 1) {
            return ['min' => 1, 'max' => $elementsOnPage];
        }
        $min = ($actualPage-1)*$elementsOnPage +1;
        $max = $actualPage*$elementsOnPage;
        return ['min' => $min, 'max' => $max];
    }

    /**
     * @param int $actualElement
     * @param int $elementsOnPage
     * @return int
     */
    public function getActualPageByElement (int $actualElement, int $elementsOnPage) {
        $actualPage = 0;
        $find = false;

        while ($find === false) {
            $actualPage++;
            $elements = $this->getMinMaxElementsOnPage($actualPage, $elementsOnPage);
            if ($actualElement >= $elements['min'] && $actualElement <= $elements['max']) {
                $find = true;
            }
        }

        return $actualPage;
    }

    /**
     * @param int $actualPage
     * @param int $countShowPages
     * @return array
     */
    public function getMainMaxPages (int $actualPage, int $countShowPages, int $sumPages) {
        $group = $this->getCountPagesOrGroups($actualPage, $countShowPages);
        $result = $this->getMinMaxElementsOnPage($group, $countShowPages);
        if ($result['max'] > $sumPages) {
            $result['max'] = $sumPages;
        }
        return $result;
    }

}