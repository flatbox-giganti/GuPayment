<?php

namespace Potelo\GuPayment;

use Carbon\Carbon;

class InvoiceBuilder
{
    /**
     * The user model that is subscribing.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $user;

    /**
     * @var array
     */
    protected $itens = [];

    /**
     * @var Carbon
     */
    protected $dueDate;

    /**
     * Create a new subscription builder instance.
     *
     * @param mixed $user
     * @param \Carbon\Carbon $dueDate
     */
    public function __construct($user, Carbon $dueDate)
    {
        $this->user = $user;
        $this->dueDate = $dueDate;
    }

    /**
     * @param $description
     * @param $price
     * @param int $quantity
     * @return \Potelo\GuPayment\InvoiceBuilder
     */
    public function addItem($price, $description = 'Nova fatura', $quantity = 1)
    {
        $this->itens[] = [
            'description' => $description,
            'quantity' => $quantity,
            'price_cents' => $price,
        ];

        return $this;
    }

    /**
     * @param $options
     * @return mixed
     */
    public function create($options)
    {
        $options['due_date'] = $this->dueDate->format('Y-m-d');

        $options['items'] = $this->itens;

        if (! array_key_exists('customer_id', $options) && $this->user->hasIuguId()) {
            $options['customer_id'] = $this->user->getIuguUserId();
        }

        return $this->user->createIuguInvoice($options);
    }

}
