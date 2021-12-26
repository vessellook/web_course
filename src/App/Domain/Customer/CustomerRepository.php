<?php
declare(strict_types=1);


namespace App\Domain\Customer;

interface CustomerRepository
{
    /**
     * @return Customer[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Customer
     * @throws CustomerNotFoundException
     */
    public function findCustomerOfId(int $id): Customer;

    /**
     * @param Customer $customer
     * @return Customer
     * @throws CustomerCanNotBeCreated
     */
    public function createCustomer(Customer $customer): Customer;

    public function updateCustomer(Customer $old, Customer $new): Customer;

    public function deleteCustomer(int $customerId): bool;
}
