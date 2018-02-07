<?php

namespace PHPDish\Bundle\PaymentBundle\Service;

use PHPDish\Bundle\PaymentBundle\Model\PaymentInterface;
use PHPDish\Bundle\UserBundle\Model\UserInterface;

interface PaymentManagerInterface
{
    /**
     * 创建payment
     * @param UserInterface|null $user
     * @return PaymentInterface
     */
    public function createPayment(UserInterface $user = null);

    /**
     * 保存payment
     * @param PaymentInterface $payment
     */
    public function savePayment(PaymentInterface $payment);

    /**
     * 发起收款
     * @param PaymentInterface $payment
     * @return mixed
     */
    public function charge(PaymentInterface $payment);
}