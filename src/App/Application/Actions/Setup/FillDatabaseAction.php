<?php

namespace App\Application\Actions\Setup;

use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\SqlScripts\CreateDatabaseScript;
use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerRepository;
use App\Domain\Order\Order;
use App\Domain\Order\OrderRepository;
use App\Domain\Product\Product;
use App\Domain\Product\ProductRepository;
use App\Domain\Transportation\Transportation;
use App\Domain\Transportation\TransportationRepository;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use DateTimeImmutable;
use Exception;
use App\Domain\DomainException\DomainRecordNotFoundException;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class FillDatabaseAction extends Action
{
    public function __construct(
        LoggerInterface                  $logger,
        private CreateDatabaseScript     $script,
        private UserRepository           $userRepository,
        private CustomerRepository       $customerRepository,
        private ProductRepository        $productRepository,
        private OrderRepository          $orderRepository,
        private TransportationRepository $transportationRepository
    )
    {
        parent::__construct($logger);
    }

    /**
     * @throws Exception
     */
    private function createProducts()
    {
        $products = [
            [
                'name' => 'Лампа',
                'uid' => 'А-12345',
                'price' => 100000
            ],
            [
                'name' => 'Радио',
                'uid' => 'А-54321',
                'price' => 200000
            ],
            [
                'name' => 'Настенные часы',
                'uid' => 'А-12121',
                'price' => 150000
            ]
        ];
        $products = array_map(fn($product) => new Product(
            id: null,
            uid: $product['uid'],
            name: $product['name'],
            price: $product['price'],
        ), $products);
        foreach ($products as $product) {
            $this->productRepository->createProduct($product);
        }
    }

    /**
     * @throws Exception
     */
    private function createUsers()
    {
        $users = [
            [
                'role' => 'director',
                'login' => 'director@progress.ru',
                'password' => 'director',
            ],
            [
                'role' => 'operator',
                'login' => 'operator@progress.ru',
                'password' => 'operator',
            ],
            [
                'role' => 'operator',
                'login' => 'operator@mail.ru',
                'password' => 'operator',
            ],
            [
                'role' => 'operator',
                'login' => 'operator2@mail.ru',
                'password' => 'operator2',
            ]
        ];
        $users = array_map(fn($user) => new User(
            id: null,
            role: $user['role'],
            login: $user['login'],
            password: $user['password']
        ), $users);
        foreach ($users as $user) {
            $this->userRepository->registerNewUser($user);
        }
    }

    /**
     * @throws Exception
     */
    private function createCustomers()
    {
        $customers = [
            [
                'name' => 'ООО Рога и Копыта',
                'phoneNumber' => null,
                'address' => null
            ],
            [
                'name' => 'ИП Шарашкина',
                'phoneNumber' => '+7999553535',
                'address' => 'Москва, ул. Дубосековская, 5'
            ],
            [
                'name' => 'ИП Кипящий',
                'phoneNumber' => null,
                'address' => null
            ]
        ];
        $customers = array_map(fn($customer) => new Customer(
            id: null,
            name: $customer['name'],
            address: $customer['address'],
            phoneNumber: $customer['phoneNumber'],
        ), $customers);
        foreach ($customers as $customer) {
            $this->customerRepository->createCustomer($customer);
        }
    }

    /**
     * @throws Exception
     */
    public function createOrders()
    {
        $orderValues = [
            [
                'customer_name' => 'ИП Кипящий',
                'address' => 'Москва, Каширское шоссе, 64 корпус 1',
                'product_uid' => 'А-12345',
                'date' => null,
                'agreement_code' => null,
                'agreement_url' => null,
                'transportations' => [
                    [
                        'planned_date' => new DateTimeImmutable('2021-12-20'),
                        'real_date' => null,
                        'number' => 30,
                        'status' => 'planned'
                    ],
                    [
                        'planned_date' => new DateTimeImmutable('2022-01-01'),
                        'real_date' => null,
                        'number' => 20,
                        'status' => 'planned'
                    ]
                ]
            ],
            [
                'customer_name' => 'ИП Кипящий',
                'address' => 'Москва, ул. Дубосековская, 5',
                'product_uid' => 'А-54321',
                'date' => null,
                'agreement_code' => null,
                'agreement_url' => null,
                'transportations' => [
                    [
                        'planned_date' => new DateTimeImmutable('2021-12-20'),
                        'real_date' => new DateTimeImmutable('2021-12-21'),
                        'number' => 10,
                        'status' => 'finished'
                    ]
                ]
            ]
        ];
        $products = $this->productRepository->findAll();
        $customers = $this->customerRepository->findAll();
        array_walk($orderValues, function (&$order) use ($products, $customers) {
            $productArr = array_filter($products, fn($product) => $product->getUid() === $order['product_uid']);
            $productArr = array_values($productArr);
            $order['product_id'] = count($productArr) > 0 ? $productArr[0]->getId() : throw new Exception();

            $customerArr = array_filter($customers, fn($customer) => $customer->getName() === $order['customer_name']);
            $customerArr = array_values($customerArr);
            $order['customer_id'] = count($customerArr) > 0 ? $customerArr[0]->getId() : throw new Exception();
        });

        $orders = array_map(fn($order) => new Order(
            id: null,
            customerId: $order['customer_id'],
            productId: $order['product_id'],
            address: $order['address'],
            date: $order['date'] ?? null,
            agreementCode: $order['agreement_code'] ?? null,
            agreementUrl: $order['agreement_url'] ?? null,
        ), $orderValues);

        array_walk($orders, fn($order) => $this->orderRepository->createOrder($order));

        for ($i = 0; $i < count($orders); $i++) {
            if (empty($orderValues[$i]['transportations'])) {
                continue;
            }
            $orderId = $orders[$i]->getId();
            $transportations = array_map(
                fn($transportation) => new Transportation(
                    id: null,
                    orderId: $orderId,
                    plannedDate: $transportation['planned_date'],
                    realDate: $transportation['real_date'] ?? null,
                    number: $transportation['number'],
                    status: $transportation['status'],
                ),
                $orderValues[$i]['transportations']
            );
            foreach ($transportations as $transportation) {
                $this->transportationRepository->createTransportation($transportation);
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $this->script->run();
        try {
            $this->createProducts();
            $this->logger->debug('products created');
            $this->createUsers();
            $this->logger->debug('users created');
            $this->createCustomers();
            $this->logger->debug('customers created');
            $this->createOrders();
            $this->logger->debug('orders and transportations created');
        } catch (Exception $exception) {
            $actionError = new ActionError($exception::class, $exception->getMessage());
            return $this->respond(new ActionPayload(statusCode: 500, error: $actionError));
        }
        return $this->respond(new ActionPayload(statusCode: 201));
    }
}
