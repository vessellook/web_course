import {customerUrl, orderUrl} from "@/api/constants";
import Order from "@/models/Order";
import {createEntity, deleteEntity, getAllEntities, getEntity, updateEntity} from "@/api/common";

let cls = Order;

/**
 *
 * @param customerId
 * @param token
 * @return {Promise<Order[]>}
 */
export function getOrders({customerId = null, token}) {
  let url = (customerId === null) ? orderUrl : `${customerUrl}/${customerId}/orders`
  return getAllEntities({url, token, cls});
}

export function getOrderWithTransportations({orderId, token}) {
  return getEntity({
    url: `${orderUrl}/${orderId}`,
    token,
    cls: Order
  });
}

export function getOrder({orderId, token}) {
  return getOrderWithTransportations({orderId, token})
    .then(obj => obj.order);
}

/**
 *
 * @param customerId
 * @param {Order} order
 * @param token
 * @param {Transportation[]} transportations
 * @returns {Promise<Order>}
 */
export function createOrder({customerId, order, token, transportations = []}) {
  return createEntity({
    url: `${customerUrl}/${customerId}/orders`,
    body: JSON.stringify({order, transportations}),
    token,
    cls
  });
}

/**
 *
 * @param orderId
 * @param {Order} oldOrder
 * @param {Order} newOrder
 * @param token
 * @returns {Promise<Order>}
 */
export function updateOrder({orderId, oldOrder, newOrder, token}) {
  return updateEntity({
    url: `${orderUrl}/${orderId}`,
    body: JSON.stringify({old: oldOrder, new: newOrder}),
    token,
    cls
  });
}

/**
 *
 * @param orderId
 * @param token
 * @returns {Promise<Response>}
 */
export function deleteOrder({orderId, token}) {
  return deleteEntity({
    url: `${orderUrl}/${orderId}`,
    token
  });
}