<?php

namespace App\Application\Actions\Setup;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\SqlScripts\CreateDatabaseScript;
use Exception;
use App\Domain\DomainException\DomainRecordNotFoundException;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class FillDatabaseAction extends DatabaseAction
{
    /**
     * @throws Exception
     */
    private function createProducts()
    {
        $products = [
            [
                'name' => 'Лампа',
                'uid' => 'А-12345',
                'price' => 100000,
                'count' => 200
            ],
            [
                'name' => 'Радио',
                'uid' => 'А-54321',
                'price' => 200000,
                'count' => 150
            ],
            [
                'name' => 'Настенные часы',
                'uid' => 'А-12121',
                'price' => 150000,
                'count' => 220
            ]
        ];
        $stmt = 'INSERT INTO product (uid, name, price, count) VALUES (:uid, :name, :price, :count)';
        $stmt = $this->pdo->prepare($stmt);
        foreach ($products as $rowData) {
            if (!$stmt->execute($rowData)) {
                throw new Exception($stmt->errorInfo()[2]);
            }
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
                'email' => 'director@progress.ru',
                'phone_number' => null,
                'password' => 'director',
                'name' => 'Борис Николаевич Золоторуков'
            ],
            [
                'role' => 'operator',
                'login' => 'operator@progress.ru',
                'email' => 'operator@progress.ru',
                'phone_number' => null,
                'password' => 'operator',
                'name' => 'Сергей Петрович Иванов'
            ],
            [
                'role' => 'customer',
                'login' => 'customer@mail.ru',
                'email' => 'customer@mail.ru',
                'phone_number' => '+79998887766',
                'password' => 'customer',
                'name' => 'Пётр Петрович Сидоров'
            ],
            [
                'role' => 'customer',
                'login' => 'customer2@mail.ru',
                'email' => 'customer2@mail.ru',
                'phone_number' => null,
                'password' => 'customer2',
                'name' => 'Николай Александрович Волков'
            ]
        ];
        $stmt = 'INSERT INTO user (role, login, email, phone_number, password_hash, name)
 VALUES (:role, :login, :email, :phone_number, :password, :name)';
        $stmt = $this->pdo->prepare($stmt);
        foreach ($users as $rowData) {
            if (!$stmt->execute($rowData)) {
                throw new Exception($stmt->errorInfo()[2]);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function createOrders()
    {
        $orders = [
            [
                'user_login' => 'customer@mail.ru',
                'address' => null,
                'status' => 'created',
                'price' => null,
                'items' => [
                    [
                        'product_uid' => 'А-54321',
                        'count' => 1
                    ]
                ]
            ],
            [
                'user_login' => 'customer2@mail.ru',
                'address' => 'Москва, Каширское шоссе, 64 корпус 1',
                'status' => 'fixed',
                'price' => 200000,
                'items' => [
                    [
                        'product_uid' => 'А-12345',
                        'count' => 2
                    ]
                ]
            ]
        ];

        foreach ($orders as $order) {
            $stmt = 'SELECT id FROM user WHERE login = :user_login';
            $stmt = $this->pdo->prepare($stmt);
            $stmt->bindParam('user_login', $order['user_login']);
            $stmt->execute();
            $row = $stmt->fetch();
            $order['user_id'] = $row['id'];

            $stmt = 'INSERT INTO `order` (user_id, address, status, price) 
VALUES (:user_id, :address, :status, :price)';
            $stmt = $this->pdo->prepare($stmt);
            $stmt->bindParam('user_id', $order['user_id']);
            $stmt->bindParam('address', $order['address']);
            $stmt->bindParam('status', $order['status']);
            $stmt->bindParam('price', $order['price']);
            if (!$stmt->execute()) {
                throw new Exception($stmt->errorInfo()[2]);
            }

            $order['id'] = $this->pdo->lastInsertId();

            if (!isset($order['items'])) {
                continue;
            }
            foreach ($order['items'] as $item) {
                $stmt = 'SELECT id FROM product WHERE uid = :product_uid';
                $stmt = $this->pdo->prepare($stmt);
                $stmt->bindParam('product_uid', $item['product_uid']);
                $stmt->execute();
                $row = $stmt->fetch();
                $item['product_id'] = $row['id'];

                $stmt = 'INSERT INTO order_product (product_id, order_id, count) 
VALUES (:product_id, :order_id, :count)';
                $stmt = $this->pdo->prepare($stmt);
                $stmt->bindParam('product_id', $item['product_id']);
                $stmt->bindParam('order_id', $order['id']);
                $stmt->bindParam('count', $item['count']);
                if (!$stmt->execute()) {
                    throw new Exception($stmt->errorInfo()[2]);
                }
            }
        }
    }

    public function __construct(
        LoggerInterface      $logger,
        PDO $pdo,
        private CreateDatabaseScript $createDatabaseScript
    ) {
        parent::__construct($logger, $pdo);
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $this->pdo->query($this->createDatabaseScript->getQuery());
        try {
            $this->createProducts();
            $this->createUsers();
            $this->createOrders();
        } catch (Exception $exception) {
            $actionError = new ActionError($exception::class, $exception->getMessage());
            return $this->respond(new ActionPayload(statusCode: 500, error: $actionError));
        }
        return $this->respond(new ActionPayload(statusCode: 201));
    }
}
