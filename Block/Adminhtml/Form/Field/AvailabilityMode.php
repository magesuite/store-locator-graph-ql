<?php

namespace MageSuite\StoreLocatorGraphQl\Block\Adminhtml\Form\Field;

class AvailabilityMode extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '';
        $htmlId = $element->getHtmlId();

        $beforeElementHtml = $element->getBeforeElementHtml();
        if ($beforeElementHtml) {
            $html .= '<label class="addbefore" for="' . $htmlId . '">' . $beforeElementHtml . '</label>';
        }


        foreach ($element->getValues() as $value) {
                $html .= $this->getHtmlForInputByValue($value, $element);
        }

        $afterElementJs = $element->getAfterElementJs();
        if ($afterElementJs) {
            $html .= $afterElementJs;
        }

        $afterElementHtml = $element->getAfterElementHtml();
        if ($afterElementHtml) {
            $html .= '<label class="addafter" for="' . $htmlId . '">' . $afterElementHtml . '</label>';
        }

        return $this->getContainerHtml($html);
    }

    private function getHtmlForInputByValue($value, $element)
    {
        $imageUrl =  $this->getViewFileUrl($value['image']);
        $checkedFlag = $value['value'] == $element->getEscapedValue();
        $content = $this->getContent($imageUrl, $value, $checkedFlag);
        $checked = $checkedFlag ? ' checked = "checked" ' : '';
        $input = '<input class="mode" type="radio" id="' . $element->getHtmlId() . '" '.$checked.' name="' . $element->getName() . '"  value="' . $value['value'] . '" ' . $this->serialize($element->getHtmlAttributes()) . '/>';

        return $this->getWrappingHtml($input.$content, $checkedFlag);
    }

    private function getContent($imageUel, $value)
    {
        return sprintf('<h4>%s</h4><img src="%s" /><div class="cs_stores_availability_mode_explanation">%s</div>', $value['label'], $imageUel, $value['description']);
    }

    private function getWrappingHtml($content, $checked)
    {
        return sprintf('<div class="cs_stores_availability_mode %s">%s</div>', $checked ? 'active' : 'inactive', $content);
    }

    private function getContainerHtml($content)
    {
        return sprintf('<div class="cs_stores_availability_mode_container">%s</div>', $content);
    }
}
