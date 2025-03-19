<?php

namespace Firebed\AadeMyData\Models;

use Firebed\AadeMyData\Traits\HasFactory;

/**
 * @version 1.0.8
 */
class PaymentMethod extends Type
{
    use HasFactory;

    protected array $expectedOrder = [
        'invoiceMark',
        'paymentMethodMark',
        'entityVatNumber',
        'paymentMethodDetails'
    ];

    protected array $casts = [
        'paymentMethodDetails' => PaymentMethodDetail::class,
    ];

    /**
     * @return int|null Μοναδικός Αριθμός Καταχώρησης Παραστατικού
     *
     * @version 1.0.8
     */
    public function getInvoiceMark(): ?int
    {
        return $this->get('invoiceMark');
    }

    /**
     * @param  int  $invoiceMark
     *
     * @return PaymentMethod
     * @version 1.0.8
     */
    public function setInvoiceMark(int $invoiceMark): static
    {
        return $this->set('invoiceMark', $invoiceMark);
    }

    /**
     * Συμπληρώνεται από την υπηρεσία
     *
     * @return int|null Μοναδικός Αριθμός Καταχώρησης Τρόπου Πληρωμής.
     *
     * @version 1.0.8
     */
    public function getPaymentMethodMark(): ?int
    {
        return $this->get('paymentMethodMark');
    }

    /**
     * @return string|null ΑΦΜ Οντότητας Αναφοράς
     *
     * @version 1.0.8
     */
    public function getEntityVatNumber(): ?string
    {
        return $this->get('entityVatNumber');
    }

    /**
     * Όταν η μέθοδος καλείται από τρίτο πρόσωπο (πχ πάροχος), ο ΑΦΜ αναφοράς
     * αποστέλλεται μέσω του πεδίου entityVatNumber, διαφορετικά το εν λόγω πεδίο
     * παραμένει κενό.
     *
     * @param  string|null  $entityVatNumber  ΑΦΜ Οντότητας Αναφοράς
     *
     * @version 1.0.8
     */
    public function setEntityVatNumber(?string $entityVatNumber): static
    {
        return $this->set('entityVatNumber', $entityVatNumber);
    }

    /**
     * @return PaymentMethodDetail[]|null Τρόπος Πληρωμής
     *
     * @version 1.0.8
     */
    public function getPaymentMethodDetails(): ?array
    {
        return $this->get('paymentMethodDetails');
    }

    /**
     * Το σύνολο των ποσών amount ανά αντικείμενο PaymentMethodType πρέπει να
     * ισούται με το totalValue του παραστατικού στο οποίο αντιστοιχεί το invoiceMark.
     *
     * @param  PaymentMethodDetail  $paymentMethodDetails  Τρόπος Πληρωμής
     *
     * @version 1.0.8
     */
    public function addPaymentMethodDetails(PaymentMethodDetail $paymentMethodDetails): static
    {
        return $this->push('paymentMethodDetails', $paymentMethodDetails);
    }

    /**
     * Το σύνολο των ποσών amount ανά αντικείμενο PaymentMethodType πρέπει να
     * ισούται με το totalValue του παραστατικού στο οποίο αντιστοιχεί το invoiceMark.
     *
     * @param  PaymentMethodDetail[]  $paymentMethodDetails  Τρόποι Πληρωμής
     *
     * @version 1.0.8
     */
    public function setPaymentMethodDetails(array $paymentMethodDetails): static
    {
        return $this->set('paymentMethodDetails', $paymentMethodDetails);
    }

    public function set($key, $value): static
    {
        if ($key === 'paymentMethodDetails' && !is_array($value)) {
            return $this->addPaymentMethodDetails($value);
        }

        return parent::set($key, $value);
    }
}