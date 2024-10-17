<?php

namespace CardknoxDevelopment\Cardknox\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Framework\View\Element\Template;

class Totals extends Template
{
    protected $_invoice = null;
    protected $_source;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get Invoice
     *
     * @return mixed
     */
    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent =  $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->getInvoice();
        $this->getSource();

        if (!$this->getSource()->getCkgiftcardAmount()) {
            return $this;
        }

        $total = new \Magento\Framework\DataObject(
            [
                'code' => 'ckgiftcardamount',
                'value' => -$this->getSource()->getCkgiftcardAmount(),
                'label' => "Cardknox Giftcard Amount",
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        $this->getInvoice()->getGrandTotal();
        $this->getSource()->getCkgiftcardAmount();
        $invoiceGrandTotal = $this->getInvoice()->getGrandTotal() - $this->getSource()->getCkgiftcardAmount();
        $this->getInvoice()->setGrandTotal($invoiceGrandTotal);

        return $this;
    }
}
