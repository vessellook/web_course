<?php
declare(strict_types=1);


namespace App\Application\Actions\Transportation;

use Psr\Http\Message\ResponseInterface as Response;

class ListTransportationsAction extends TransportationAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        if (isset($this->args['orderId'])) {
            $transportations = $this->transportationRepository->findAllOfOrder((int) $this->args['orderId']);
        } else {
            $transportations = $this->transportationRepository->findAll();
        }
        return $this->respondWithData($transportations);
    }
}
